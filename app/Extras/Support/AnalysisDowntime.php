<?php

declare(strict_types=1);

namespace App\Extras\Support;

use App\Extras\Interfaces\ExportableReport;
use App\Models\Downtime;
use App\Models\Factory;
use App\Models\Plant;
use App\Models\Production;
use App\Models\ProductionLine;
use App\Models\ProductionOrder;
use App\Models\RejectGroup;
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

class AnalysisDowntime extends ResultData implements Arrayable, Jsonable, JsonSerializable, Stringable, ExportableReport
{
    public $data;
    public $plant;
    public $dateStart;
    public $dateEnd;
    public $workCenter;

    public static function create(Plant $plant, string $workCenterUid, string $dateStart, string $dateEnd)
    {

        $analysis = new AnalysisDowntime();

        $rows = DB::connection($plant->onPlantDb()->getPlantConnection())
            ->table(WorkCenter::TABLE_NAME)
            ->join(Production::TABLE_NAME, WorkCenter::TABLE_NAME . '.id', '=', Production::TABLE_NAME . '.work_center_id', 'left')
            ->join(ProductionLine::TABLE_NAME, Production::TABLE_NAME . '.id', '=', ProductionLine::TABLE_NAME . '.production_id', 'left')
            ->join(ProductionOrder::TABLE_NAME, ProductionLine::TABLE_NAME . '.production_order_id', '=', ProductionOrder::TABLE_NAME . '.id', 'left')

            ->where(WorkCenter::TABLE_NAME . '.plant_id', '=', $plant->id)
            ->where(ProductionLine::TABLE_NAME . '.actual_output', '>', 0)
            ->where(Production::TABLE_NAME . '.shift_date', '>=', $dateStart)
            ->where(Production::TABLE_NAME . '.shift_date', '<=', $dateEnd)

            ->where(WorkCenter::TABLE_NAME . '.enabled', '=', 1)
            ->where(WorkCenter::TABLE_NAME . '.uid', '=', $workCenterUid)
            ->orderBy(Production::TABLE_NAME . '.started_at')
            ->select([

                DB::raw(WorkCenter::TABLE_NAME . '.uid as work_center_uid'),
                DB::raw(WorkCenter::TABLE_NAME . '.name as work_center_name'),

                DB::raw(Production::TABLE_NAME . '.id as production_id'),

                DB::raw(Production::TABLE_NAME . '.shift_date as shift_date'),
                DB::raw(Production::TABLE_NAME . '.shift_type_id as shift_type_id'),

                DB::raw(Production::TABLE_NAME . '.runtime_summary_cache as runtime_summary'),

                DB::raw(ProductionLine::TABLE_NAME . '.line_no as line_no'),

                DB::raw(ProductionOrder::TABLE_NAME . '.order_no as order_no'),

                // DB::raw(ProductionLine::TABLE_NAME . '.availability as availability'),
                // DB::raw(ProductionLine::TABLE_NAME . '.performance as performance'),
                // DB::raw(ProductionLine::TABLE_NAME . '.quality as quality'),
                // DB::raw(ProductionLine::TABLE_NAME . '.oee as oee'),
                // DB::raw(ProductionLine::TABLE_NAME . '.overall_summary as overall_summary'),
                // DB::raw(ProductionLine::TABLE_NAME . '.plan_quantity as plan_quantity'),


                DB::raw(ProductionLine::TABLE_NAME . '.part_data as part_data'),

                DB::raw(Production::TABLE_NAME . '.started_at as started_at'),
                DB::raw(Production::TABLE_NAME . '.stopped_at as stopped_at'),

            ])
            ->get();



        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $plant->onPlantDb()->workCenters()->where(WorkCenter::TABLE_NAME . '.uid', $workCenterUid)->first();

        $downtimes = $workCenter->downtimes()->get(); //('category', Downtime::TABLE_NAME . '.id')->toArray();

        //Get All downtimes
        $downtimeData = [];
        foreach ($downtimes as $downtime) {
            $downtimeData[$downtime->id] = [
                'id' => $downtime->id,
                'type' => $downtime->downtime_type_id,
                'name' => $downtime->category,
                'duration' => 0,
            ];
        };

        $downtimeData['_unplan_die_change'] = [
            'id' => 0,
            'type' => 3, //3 for die change
            'name' => 'UNPLAN DIE-CHANGE',
            'duration' => 0,
        ];


        $data = [
            'total_runtimes_plan' => 0,
            'total_downtimes_unplan' => 0,
            'downtime_percentage' => 0,
            'downtimes' => [],
            'data' => []
        ];
        $no = 0;

        $addedProductionIds = []; //store production id already calculated, since multiple production line share same production session

        foreach ($rows as &$row) {

            $row->runtimes_plan = 0;
            $row->downtimes_unplan = 0;
            $row->downtimes_unplan_machine = 0;
            $row->downtimes_unplan_human = 0;
            $row->downtimes_unplan_die_change = 0;
            $row->downtime_percentage = 0;




            //count downtimes
            if ($row->runtime_summary) {
                $runtimeSummary = json_decode($row->runtime_summary, true);
                if ($runtimeSummary) {
                    if (!in_array($row->production_id, $addedProductionIds)) {

                        foreach ($runtimeSummary['downtimes']['by_id'] as $downtimeId => $downtimeById) {

                            if (isset($downtimeData[$downtimeId])) {
                                $downtimeData[$downtimeId]['duration'] += $downtimeById['duration'];
                            }
                        }
                        $data['total_runtimes_plan'] += $runtimeSummary['runtimes']['plan']['duration'] ?? 0;
                        $data['total_downtimes_unplan'] += $runtimeSummary['downtimes']['unplan']['duration'] ?? 0;
                        $downtimeData['_unplan_die_change']['duration'] += $runtimeSummary['downtimes']['unplan_die_change']['duration'] ?? 0;
                        //TODO: confirm use total downtime unplan, (include unplan die change)
                        $addedProductionIds[] = $row->production_id;
                    }


                    $row->runtimes_plan = $runtimeSummary['runtimes']['plan']['duration'] ?? 0;
                    $row->downtimes_unplan = $runtimeSummary['downtimes']['unplan']['duration'] ?? 0;
                    $row->downtimes_unplan_machine = $runtimeSummary['downtimes']['unplan_machine']['duration'] ?? 0;
                    $row->downtimes_unplan_human = $runtimeSummary['downtimes']['unplan_human']['duration'] ?? 0;
                    $row->downtimes_unplan_die_change = $runtimeSummary['downtimes']['unplan_die_change']['duration'] ?? 0;

                    if ($row->runtimes_plan > 0)
                        $row->downtime_percentage = $row->downtimes_unplan / $row->runtimes_plan;
                }
            }

            //-------------

            $partData = json_decode($row->part_data);

            $row->no = ++$no;
            $row->part_no = $partData->part_no;
            $row->part_name = $partData->name;
            if ($plant)
                $analysis->workCenter = $plant->workCenters()->where('uid', $workCenterUid)->first();

            $data['data'][] = $row;

            unset($row);
        }

        if ($data['total_runtimes_plan'])
            $data['downtime_percentage'] =  $data['total_downtimes_unplan'] / $data['total_runtimes_plan'];


        $downtimeRefId = [];
        //Remove zeros
        foreach ($downtimeData as $key => $value) {
            if (!$value['duration']) {
                unset($downtimeData[$key]);
                continue;
            }
            $downtimeRefId[$key] = $value;
        }

        //sort by highest
        usort($downtimeData, function ($a, $b) {
            return $b['duration'] <=> $a['duration'];
        });

        //add downtime column to rows
        foreach ($rows as &$row) {
            foreach ($downtimeRefId as $key => $downtime) {
                $label = '_' . $downtime['name'];
                $row->$label = 0;
            }

            if ($row->runtime_summary) {
                $runtimeSummary = json_decode($row->runtime_summary, true);
                if ($runtimeSummary) {

                    foreach ($runtimeSummary['downtimes']['by_id'] as $downtimeId => $downtimeById) {

                        if (isset($downtimeRefId[$downtimeId])) {

                            $label = '_' . $downtimeRefId[$downtimeId]['name'];
                            $row->$label = $downtimeById['occurance'];
                        }
                    }
                }
            }

            unset($row);
        }



        $data['downtimes'] = $downtimeData;

        $analysis->data = $data;
        $analysis->plant = $plant;
        $analysis->dateStart = $dateStart;
        $analysis->dateEnd = $dateEnd;

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

        $sheet->setCellValue('A' . $row, 'Total Working Time');
        $sheet->setCellValue('B' . $row, (isset($this->data['total_runtimes_plan']) && is_numeric($this->data['total_runtimes_plan']) ? $this->data['total_runtimes_plan'] / 3600 : '-') ?? '-');
        $sheet->setCellValue('C' . $row, 'HRS');
        $row++;

        $sheet->setCellValue('A' . $row, 'Total Downtime');
        $sheet->setCellValue('B' . $row, (isset($this->data['total_downtimes_unplan']) && is_numeric($this->data['total_downtimes_unplan']) ? $this->data['total_downtimes_unplan'] / 3600 : '-') ?? '-');
        $sheet->setCellValue('C' . $row, 'HRS');
        $row++;

        $sheet->setCellValue('A' . $row, 'Downtime Percentage');
        $sheet->setCellValue('B' . $row, (isset($this->data['downtime_percentage']) && is_numeric($this->data['downtime_percentage']) ? $this->data['downtime_percentage'] * 100 : '-') . '%');
        $row++;

        $row++;

        $shiftText = [
            ShiftType::DAY_SHIFT => 'Day',
            ShiftType::NIGHT_SHIFT => 'Night'
        ];
        $sheet->setCellValue('A' . $row, 'Downtime');
        $sheet->setCellValue('B' . $row, 'Duration (minutes)');
        foreach ($this->data['downtimes'] as $downtime) {
            $sheet->setCellValue('A' . $row, $downtime['name']);
            $sheet->setCellValue('B' . $row, (isset($downtime['duration']) && is_numeric($downtime['duration']) ? $downtime['duration'] / 60 : '-'));
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
        $sheet->setCellValue('I' . $row, 'Total Downtime');
        $sheet->setCellValue('J' . $row, 'Unplan Die-Change');
        $sheet->setCellValue('K' . $row, 'Machine Downtime');
        $sheet->setCellValue('L' . $row, 'Human Downtime');
        $sheet->setCellValue('M' . $row, 'Downtime Percentage');
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
                $sheet->setCellValue('I' . $row, (isset($item->downtimes_unplan) && is_numeric($item->downtimes_unplan) ? $item->downtimes_unplan / 3600 : '-'));
                $sheet->setCellValue('J' . $row, (isset($item->downtimes_unplan_die_change) && is_numeric($item->downtimes_unplan_die_change) ? $item->downtimes_unplan_die_change / 3600 : '-'));
                $sheet->setCellValue('K' . $row, (isset($item->downtimes_unplan_machine) && is_numeric($item->downtimes_unplan_machine) ? $item->downtimes_unplan_machine / 3600 : '-'));
                $sheet->setCellValue('L' . $row, (isset($item->downtimes_unplan_human) && is_numeric($item->downtimes_unplan_human) ? $item->downtimes_unplan_human / 3600 : '-'));
                $sheet->setCellValue('M' . $row, (isset($item->downtime_percentage) && is_numeric($item->downtime_percentage) ? $item->downtime_percentage * 100 : '-') . '%');
                $row++;
            }
        }

        $filename = "analysis_downtime_" . $workCenter->uid ?? '' . $this->dateStart . '_' . $this->dateEnd;
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
