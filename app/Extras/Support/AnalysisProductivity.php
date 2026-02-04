<?php

declare(strict_types=1);

namespace App\Extras\Support;

use App\Extras\Interfaces\ExportableReport;
use App\Models\Factory;
use App\Models\Plant;
use App\Models\Production;
use App\Models\ProductionLine;
use App\Models\ProductionOrder;
use App\Models\ShiftType;
use App\Models\WorkCenter;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\DB;
use JsonSerializable;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Stringable;

class AnalysisProductivity extends ResultData implements Arrayable, Jsonable, JsonSerializable, Stringable, ExportableReport
{
    public $data;
    public $plant;
    public $dateStart;
    public $dateEnd;
    public $workCenter;

    public static function create(Plant $plant, string $workCenterUid, string $dateStart, string $dateEnd)
    {

        $analysis = new AnalysisProductivity();

        $rows = DB::connection($plant->onPlantDb()->getPlantConnection())
            ->table(WorkCenter::TABLE_NAME)
            ->join(Production::TABLE_NAME, WorkCenter::TABLE_NAME . '.id', '=', Production::TABLE_NAME . '.work_center_id', 'left')
            ->join(ProductionLine::TABLE_NAME, Production::TABLE_NAME . '.id', '=', ProductionLine::TABLE_NAME . '.production_id', 'left')
            ->join(ProductionOrder::TABLE_NAME, ProductionLine::TABLE_NAME . '.production_order_id', '=', ProductionOrder::TABLE_NAME . '.id', 'left')

            ->where(WorkCenter::TABLE_NAME . '.plant_id', '=', $plant->id)

            ->where(Production::TABLE_NAME . '.shift_date', '>=', $dateStart)
            ->where(Production::TABLE_NAME . '.shift_date', '<=', $dateEnd)

            ->where(WorkCenter::TABLE_NAME . '.enabled', '=', 1)
            ->where(WorkCenter::TABLE_NAME . '.uid', '=', $workCenterUid)
            ->where(ProductionLine::TABLE_NAME . '.actual_output', '>', 0)
            ->orderBy(Production::TABLE_NAME . '.started_at')
            ->select([

                DB::raw(WorkCenter::TABLE_NAME . '.uid as work_center_uid'),
                DB::raw(WorkCenter::TABLE_NAME . '.name as work_center_name'),

                DB::raw(Production::TABLE_NAME . '.shift_date as shift_date'),
                DB::raw(Production::TABLE_NAME . '.shift_type_id as shift_type_id'),

                DB::raw(ProductionLine::TABLE_NAME . '.line_no as line_no'),

                DB::raw(ProductionOrder::TABLE_NAME . '.order_no as order_no'),

                // DB::raw(ProductionLine::TABLE_NAME . '.availability as availability'),
                DB::raw(ProductionLine::TABLE_NAME . '.performance as performance'),
                // DB::raw(ProductionLine::TABLE_NAME . '.quality as quality'),

                DB::raw(Production::TABLE_NAME . '.runtime_summary_cache as runtime_summary'),

                DB::raw(ProductionLine::TABLE_NAME . '.overall_summary as overall_summary'),
                DB::raw(ProductionLine::TABLE_NAME . '.plan_quantity as plan_quantity'),
                DB::raw(ProductionLine::TABLE_NAME . '.actual_output as actual_output'),
                DB::raw(ProductionLine::TABLE_NAME . '.hourly_summary as hourly_summary'),


                DB::raw(ProductionLine::TABLE_NAME . '.part_data as part_data'),

                DB::raw(Production::TABLE_NAME . '.started_at as started_at'),
                DB::raw(Production::TABLE_NAME . '.stopped_at as stopped_at'),

            ])
            ->get();


        $data = [
            'total_standard_output' => 0,
            'total_actual_output' => 0,
            'performance' => 0,
            'shifts' => [
                [
                    'shift_type_id' => 1,
                    'hourly_data' => [],
                ],
                [
                    'shift_type_id' => 2,
                    'hourly_data' => [],
                ],
            ],
            'data' => []
        ];
        $plantTimeZone = $plant->getLocalDateTimeZone();
        $no = 0;
        foreach ($rows as &$row) {
            // $data['average_oee'] += $row->oee;
            // $data['average_availability'] += $row->availability;
            // $data['average_performance'] += $row->performance;
            // $data['average_quality'] += $row->quality;

            $partData = json_decode($row->part_data);
            $overallSummary = json_decode($row->overall_summary);
            $row->no = ++$no;
            $row->part_no = $partData->part_no;
            $row->part_name = $partData->name;

            $dtStart = \DateTime::createFromFormat('Y-m-d H:i:s', $row->started_at);
            if (!$dtStart)
                continue;

            if ($row->runtime_summary) {
                $runtimeSummary = json_decode($row->runtime_summary, true);
                if ($runtimeSummary) {
                    $row->runtimes_plan = $runtimeSummary['runtimes']['plan']['duration'] ?? 0;
                }
            }

            $dtDayStart = \DateTime::createFromFormat('Y-m-d H:i:s', $row->shift_date . ' 00:00:00', $plantTimeZone);

            if ($overallSummary) {
                $row->standard_output = $overallSummary->standard_output ?? 0;
            } else {
                $row->standard_output = 0;
            }

            $data['total_standard_output'] += $row->standard_output;
            $data['total_actual_output'] += $row->actual_output;


            $hourly_summary = json_decode($row->hourly_summary);
            if ($hourly_summary) {
                foreach ($data['shifts'] as &$shift) {
                    if ($shift['shift_type_id'] != $row->shift_type_id) {
                        unset($shift);
                        continue;
                    }

                    //merge hourly summary
                    foreach ($hourly_summary as $key => $block) {
                        $dtStartBlock = new \DateTime($block->start);
                        $hourOffset = floor(($dtStartBlock->getTimestamp() - $dtDayStart->getTimestamp()) / 3600);
                        $localHour = $hourOffset;
                        if ($localHour >= 24)
                            $localHour -= 24;

                        if (!isset($shift['hourly_data'][$hourOffset]))
                            $shift['hourly_data'][$hourOffset] = ['start' => sprintf('%02d:00', $localHour), 'line_data' => []];
                        if (!isset($shift['hourly_data'][$hourOffset]['line_data'][$row->line_no]))
                            $shift['hourly_data'][$hourOffset]['line_data'][$row->line_no] = ['count' => 0, 'item_count' => 0];

                        $shift['hourly_data'][$hourOffset]['line_data'][$row->line_no]['count'] += $block->actual_output ?? 0;
                        $shift['hourly_data'][$hourOffset]['line_data'][$row->line_no]['item_count']++;
                    }

                    unset($shift);
                }
            }

            $data['data'][] = $row;

            unset($row);
        }

        if ($data['total_standard_output'] > 0)
            $data['performance'] = $data['total_actual_output'] / $data['total_standard_output'];
        else
            $data['performance'] = null;


        foreach ($data['shifts'] as &$shift) {
            foreach ($shift['hourly_data'] as $key => &$block) {
                foreach ($block['line_data'] as &$lineData) {
                    if ($lineData['item_count'] > 1)
                        $lineData['count'] /= $lineData['item_count'];
                    unset($lineData);
                }
                unset($block);
            }
            unset($shift);
        }

        $analysis->data = $data;
        $analysis->plant = $plant;
        $analysis->dateStart = $dateStart;
        $analysis->dateEnd = $dateEnd;
        if ($plant)
            $analysis->workCenter = $plant->workCenters()->where('uid', $workCenterUid)->first();

        return $analysis;
    }
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
    public function toArray()
    {

        $return = [];

        /*
            average_oee
            average_availability
            average_performance
            average_quality
        */


        /* foreach shift
            average_oee
            average_availability
            average_performance
            average_quality
        */

        /*foreach production lines (datatable)
            No
            Date
            Shift
            Line
            Production
            Production Order
            Part Number
            Part Name
            A %
            P %
            Q %
            OEE %
        */

        $class_vars = get_class_vars(get_class($this));

        $return = [];
        foreach ($class_vars as $name => $value) {
            $return[$name] = $this->$name;
        }

        return $return;
    }
    public function __toString()
    {
        return $this->toJson();
    }
    public function export(string $format = 'xlsx')
    {
        /** @var \App\Models\Plant $plant */
        $plant = $this->plant;

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $this->workCenter;

        if (!$plant || !$workCenter)
            abort(404);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $row = 1;

        $sheet->setCellValue('A' . $row, 'Operational Analysis - Productivity');
        $row++;
        $row++;

        $sheet->setCellValue('A' . $row, 'Generated At');
        $sheet->setCellValue('B' . $row, $plant->getLocalDateTime());
        $row++;

        $sheet->setCellValue('A' . $row, 'Plant');
        $sheet->setCellValue('B' . $row, $this->plant->name ?? '-');
        $row++;

        $sheet->setCellValue('A' . $row, 'Work Center');
        $sheet->setCellValue('B' . $row, $workCenter->name ?? '-');
        $row++;

        $sheet->setCellValue('A' . $row, 'Date Start');
        $sheet->setCellValue('B' . $row, $this->dateStart ?? '-');
        $row++;

        $sheet->setCellValue('A' . $row, 'Date End');
        $sheet->setCellValue('B' . $row, $this->dateEnd ?? '-');
        $row++;

        $row++;

        $sheet->setCellValue('A' . $row, 'Total Plan Output');
        $sheet->setCellValue('B' . $row, $this->data['total_standard_output'] ?? '-');
        $sheet->setCellValue('C' . $row, 'PCS');
        $row++;

        $sheet->setCellValue('A' . $row, 'Total Actual Output');
        $sheet->setCellValue('B' . $row, $this->data['total_actual_output'] ?? '-');
        $sheet->setCellValue('C' . $row, 'PCS');
        $row++;

        $sheet->setCellValue('A' . $row, 'Productivity Percentage');
        $sheet->setCellValue('B' . $row, (isset($this->data['performance']) && is_numeric($this->data['performance']) ? $this->data['performance'] * 100 : '-') . '%');
        $row++;

        $row++;

        $dayShiftData = null;
        $nightShiftData = null;
        if (isset($this->data['shifts']) && is_array($this->data['shifts'])) {
            foreach ($this->data['shifts'] as $shiftData) {
                if (isset($shiftData['shift_type_id'])) {
                    if ($shiftData['shift_type_id'] == ShiftType::DAY_SHIFT)
                        $dayShiftData = $shiftData;
                    elseif ($shiftData['shift_type_id'] == ShiftType::NIGHT_SHIFT)
                        $nightShiftData = $shiftData;
                }
            }
        }

        $shiftText = [
            ShiftType::DAY_SHIFT => 'Day',
            ShiftType::NIGHT_SHIFT => 'Night'
        ];

        $sheet->setCellValue('A' . $row, 'Average Hourly Production');
        $row++;
        $row++;
        foreach ($this->data['shifts'] as $shiftData) {
            if (!isset($shiftData['shift_type_id'], $shiftText[$shiftData['shift_type_id']], $shiftData['hourly_data']) || !is_array($shiftData['hourly_data']))
                continue;

            $sheet->setCellValue('A' . $row, $shiftText[$shiftData['shift_type_id']]);
            $row++;

            $lineLabelRow = $row;
            $sheet->setCellValue('A' . $lineLabelRow, 'Time');
            $row++;

            $lineIndex = [];

            foreach ($shiftData['hourly_data'] as $hour => $hourlyData) {
                if (!isset($hourlyData['line_data']) || !is_array($hourlyData['line_data']))
                    continue;

                if (!is_numeric($hour))
                    continue;

                $startHour = ($hour < 10) ? '0' . $hour : $hour;
                $endHour = (($hour + 1) < 10) ? '0' . ($hour + 1) : ($hour + 1);
                $sheet->setCellValue('A' . $row, $startHour . ':00 - ' . $endHour . ':00');
                foreach ($hourlyData['line_data'] as $line => $lineData) {
                    if (!isset($lineIndex[$line])) {
                        $lineIndex[$line] = count($lineIndex);
                        $sheet->setCellValue(chr(ord('B') + $lineIndex[$line]) . $lineLabelRow, 'Line ' . $line);
                    }

                    $sheet->setCellValue(chr(ord('B') + $lineIndex[$line]) . $row, $lineData['count']);
                }
                $row++;
            }

            $row++;
        }

        $row++;

        $sheet->setCellValue('A' . $row, 'No');
        $sheet->setCellValue('B' . $row, 'Date');
        $sheet->setCellValue('C' . $row, 'Shift');
        $sheet->setCellValue('D' . $row, 'Line');
        $sheet->setCellValue('E' . $row, 'Production Order');
        $sheet->setCellValue('F' . $row, 'Part Number');
        $sheet->setCellValue('G' . $row, 'Part Name');
        $sheet->setCellValue('H' . $row, 'Total Working Hours');
        $sheet->setCellValue('I' . $row, 'Total Plan');
        $sheet->setCellValue('J' . $row, 'Total Standard Output');
        $sheet->setCellValue('K' . $row, 'Total Actual Output');
        $sheet->setCellValue('L' . $row, 'Productivity');
        $row++;

        if (isset($this->data['data']) && is_array($this->data['data'])) {
            $n = 1;
            foreach ($this->data['data'] as $item) {
                $sheet->setCellValue('A' . $row, $n++);
                $sheet->setCellValue('B' . $row, $item->shift_date ?? '-');
                $sheet->setCellValue('C' . $row, $shiftText[$item->shift_type_id] ?? '-');
                $sheet->setCellValue('D' . $row, $item->line_no ?? '-');
                $sheet->setCellValueExplicit('E' . $row, $item->order_no, DataType::TYPE_STRING2);
                $sheet->setCellValueExplicit('F' . $row, $item->part_no ?? '-', DataType::TYPE_STRING2);
                $sheet->setCellValueExplicit('G' . $row, $item->part_name ?? '-', DataType::TYPE_STRING2);
                $sheet->setCellValue('H' . $row, (isset($item->runtimes_plan) && is_numeric($item->runtimes_plan) ? $item->runtimes_plan / 3600 : '-'));
                $sheet->setCellValue('I' . $row, (isset($item->plan_quantity) && is_numeric($item->plan_quantity) ? $item->plan_quantity : '-'));
                $sheet->setCellValue('J' . $row, (isset($item->standard_output) && is_numeric($item->standard_output) ? $item->standard_output : '-'));
                $sheet->setCellValue('K' . $row, (isset($item->actual_output) && is_numeric($item->actual_output) ? $item->actual_output : '-'));
                $sheet->setCellValue('L' . $row, (isset($item->performance) && is_numeric($item->performance) ? $item->performance * 100 : '-') . '%');
                $row++;
            }
        }

        $filename = "analysis_productivity_" . $workCenter->uid ?? '' . $this->dateStart . '_' . $this->dateEnd;
        if ($format == 'csv') {
            $writer = new Csv($spreadsheet);
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . urlencode($filename) . '.csv"');
            $writer->save('php://output');
        } else {

            foreach ($sheet->getColumnIterator() as $column) {
                $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . urlencode($filename) . '.xlsx"');
            $writer->save('php://output');
        }
        return;
    }
}
