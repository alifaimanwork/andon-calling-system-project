<?php

declare(strict_types=1);

namespace App\Extras\Support;

use App\Extras\Interfaces\ExportableReport;
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

class AnalysisOee extends ResultData implements Arrayable, Jsonable, JsonSerializable, Stringable, ExportableReport
{
    public $data;
    public $plant;
    public $dateStart;
    public $dateEnd;
    public $workCenter;

    public static function create(Plant $plant, string $workCenterUid, string $dateStart, string $dateEnd)
    {

        $analysis = new AnalysisOee();

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

                DB::raw(ProductionLine::TABLE_NAME . '.availability as availability'),
                DB::raw(ProductionLine::TABLE_NAME . '.performance as performance'),
                DB::raw(ProductionLine::TABLE_NAME . '.quality as quality'),
                DB::raw(ProductionLine::TABLE_NAME . '.oee as oee'),

                DB::raw(ProductionLine::TABLE_NAME . '.part_data as part_data'),

                DB::raw(Production::TABLE_NAME . '.started_at as started_at'),


            ])
            ->get();


        $data = [
            'average_oee' => 0,
            'average_availability' => 0,
            'average_performance' => 0,
            'average_quality' => 0,
            'shifts' => [
                [
                    'shift_type_id' => 1,
                    'average_oee' => 0,
                    'average_availability' => 0,
                    'average_performance' => 0,
                    'average_quality' => 0,
                    'item_count' => 0,
                ],
                [
                    'shift_type_id' => 2,
                    'average_oee' => 0,
                    'average_availability' => 0,
                    'average_performance' => 0,
                    'average_quality' => 0,
                    'item_count' => 0,
                ],
            ],
            'data' => []
        ];

        $no = 0;
        foreach ($rows as &$row) {
            $data['average_oee'] += $row->oee;
            $data['average_availability'] += $row->availability;
            $data['average_performance'] += $row->performance;
            $data['average_quality'] += $row->quality;

            $partData = json_decode($row->part_data);

            $row->no = ++$no;
            $row->part_no = $partData->part_no;
            $row->part_name = $partData->name;

            foreach ($data['shifts'] as &$shift) {
                if ($shift['shift_type_id'] != $row->shift_type_id)
                    continue;

                $shift['average_oee'] += $row->oee;
                $shift['average_availability'] += $row->availability;
                $shift['average_performance'] += $row->performance;
                $shift['average_quality'] += $row->quality;
                $shift['item_count']++;

                unset($shift);
            }

            $data['data'][] = $row;

            unset($row);
        }

        if (count($rows) > 0) {

            $data['average_oee'] /= count($rows);
            $data['average_availability'] /= count($rows);
            $data['average_performance'] /= count($rows);
            $data['average_quality'] /= count($rows);

            foreach ($data['shifts'] as &$shift) {

                if ($shift['item_count'] > 0) {
                    $shift['average_oee'] /= $shift['item_count'];
                    $shift['average_availability'] /= $shift['item_count'];
                    $shift['average_performance'] /= $shift['item_count'];
                    $shift['average_quality'] /= $shift['item_count'];
                }
                unset($shift);
            }
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

        $sheet->setCellValue('A' . $row, 'Operational Analysis - OEE');
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
        $sheet->setCellValue('A' . $row, 'Item\Shift');
        $sheet->setCellValue('B' . $row, 'Day');
        $sheet->setCellValue('C' . $row, 'Night');
        $sheet->setCellValue('D' . $row, 'Overall');
        $row++;

        $sheet->setCellValue('A' . $row, 'Average OEE');
        $sheet->setCellValue('B' . $row, (isset($dayShiftData['average_oee']) && is_numeric($dayShiftData['average_oee']) ? $dayShiftData['average_oee'] * 100 : '-') . '%');
        $sheet->setCellValue('C' . $row, (isset($nightShiftData['average_oee']) && is_numeric($nightShiftData['average_oee']) ? $nightShiftData['average_oee'] * 100 : '-') . '%');
        $sheet->setCellValue('D' . $row, (isset($this->data['average_oee']) && is_numeric($this->data['average_oee']) ? $this->data['average_oee'] * 100 : '-') . '%');
        $row++;

        $sheet->setCellValue('A' . $row, 'Average Availability');
        $sheet->setCellValue('B' . $row, (isset($dayShiftData['average_availability']) && is_numeric($dayShiftData['average_availability']) ? $dayShiftData['average_availability'] * 100 : '-') . '%');
        $sheet->setCellValue('C' . $row, (isset($nightShiftData['average_availability']) && is_numeric($nightShiftData['average_availability']) ? $nightShiftData['average_availability'] * 100 : '-') . '%');
        $sheet->setCellValue('D' . $row, (isset($this->data['average_availability']) && is_numeric($this->data['average_availability']) ? $this->data['average_availability'] * 100 : '-') . '%');
        $row++;

        $sheet->setCellValue('A' . $row, 'Average Performance');
        $sheet->setCellValue('B' . $row, (isset($dayShiftData['average_performance']) && is_numeric($dayShiftData['average_performance']) ? $dayShiftData['average_performance'] * 100 : '-') . '%');
        $sheet->setCellValue('C' . $row, (isset($nightShiftData['average_performance']) && is_numeric($nightShiftData['average_performance']) ? $nightShiftData['average_performance'] * 100 : '-') . '%');
        $sheet->setCellValue('D' . $row, (isset($this->data['average_performance']) && is_numeric($this->data['average_performance']) ? $this->data['average_performance'] * 100 : '-') . '%');
        $row++;

        $sheet->setCellValue('A' . $row, 'Average Quality');
        $sheet->setCellValue('B' . $row, (isset($dayShiftData['average_quality']) && is_numeric($dayShiftData['average_quality']) ? $dayShiftData['average_quality'] * 100 : '-') . '%');
        $sheet->setCellValue('C' . $row, (isset($nightShiftData['average_quality']) && is_numeric($nightShiftData['average_quality']) ? $nightShiftData['average_quality'] * 100 : '-') . '%');
        $sheet->setCellValue('D' . $row, (isset($this->data['average_quality']) && is_numeric($this->data['average_quality']) ? $this->data['average_quality'] * 100 : '-') . '%');
        $row++;

        $row++;

        $sheet->setCellValue('A' . $row, 'No');
        $sheet->setCellValue('B' . $row, 'Date');
        $sheet->setCellValue('C' . $row, 'Shift');
        $sheet->setCellValue('D' . $row, 'Line');
        $sheet->setCellValue('E' . $row, 'Production Order');
        $sheet->setCellValue('F' . $row, 'Part Number');
        $sheet->setCellValue('G' . $row, 'Part Name');
        $sheet->setCellValue('H' . $row, 'Availability');
        $sheet->setCellValue('I' . $row, 'Performance');
        $sheet->setCellValue('J' . $row, 'Quality');
        $sheet->setCellValue('K' . $row, 'OEE');
        $row++;

        $shiftText = [
            ShiftType::DAY_SHIFT => 'Day',
            ShiftType::NIGHT_SHIFT => 'Night'
        ];

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
                $sheet->setCellValue('H' . $row, (isset($item->availability) && is_numeric($item->availability) ? $item->availability * 100 : '-') . '%');
                $sheet->setCellValue('I' . $row, (isset($item->performance) && is_numeric($item->performance) ? $item->performance * 100 : '-') . '%');
                $sheet->setCellValue('J' . $row, (isset($item->quality) && is_numeric($item->quality) ? $item->quality * 100 : '-') . '%');
                $sheet->setCellValue('K' . $row, (isset($item->oee) && is_numeric($item->oee) ? $item->oee * 100 : '-') . '%');
                $row++;
            }
        }

        $filename = "analysis_oee_" . $workCenter->uid ?? '' . $this->dateStart . '_' . $this->dateEnd;
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
