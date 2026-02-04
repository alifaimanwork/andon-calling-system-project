<?php

declare(strict_types=1);

namespace App\Extras\Support;

use App\Extras\Interfaces\ExportableReport;
use App\Models\Factory;
use App\Models\Plant;
use App\Models\Production;
use App\Models\ProductionLine;
use App\Models\ProductionOrder;
use App\Models\RejectGroup;
use App\Models\RejectType;
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

class AnalysisQuality extends ResultData implements Arrayable, Jsonable, JsonSerializable, Stringable, ExportableReport
{
    public $data;
    public $plant;
    public $dateStart;
    public $dateEnd;
    public $workCenter;

    public static function create(Plant $plant, string $workCenterUid, string $dateStart, string $dateEnd)
    {

        $analysis = new AnalysisQuality();

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
                // DB::raw(ProductionLine::TABLE_NAME . '.performance as performance'),
                // DB::raw(ProductionLine::TABLE_NAME . '.quality as quality'),
                // DB::raw(ProductionLine::TABLE_NAME . '.oee as oee'),
                // DB::raw(ProductionLine::TABLE_NAME . '.overall_summary as overall_summary'),
                // DB::raw(ProductionLine::TABLE_NAME . '.plan_quantity as plan_quantity'),
                DB::raw(ProductionLine::TABLE_NAME . '.actual_output as actual_output'),
                DB::raw(ProductionLine::TABLE_NAME . '.reject_count as reject_count'),
                DB::raw(ProductionLine::TABLE_NAME . '.reject_summary as reject_summary'),


                DB::raw(ProductionLine::TABLE_NAME . '.part_data as part_data'),

                DB::raw(Production::TABLE_NAME . '.started_at as started_at'),
                DB::raw(Production::TABLE_NAME . '.stopped_at as stopped_at'),

            ])
            ->get();


        //Get All rejects
        $rejectData = [];

        $data = [
            'total_actual_output' => 0,
            'total_reject_count' => 0,
            'reject_percentage' => 0,
            'defects' => [],
            'data' => []
        ];
        $no = 0;


        foreach ($rows as &$row) {

            $row->reject_setting = 0;
            $row->reject_process = 0;
            $row->reject_material = 0;

            //----- Reject chart data -----
            $rejects = json_decode($row->part_data)->part_reject_types;
            foreach ($rejects as $reject) {

                $typeName = '';
                switch ($reject->reject_group_id) {
                    case RejectGroup::REJECT_SETTING:
                        $typeName = ' (SETTING)';
                        break;
                    case RejectGroup::REJECT_MATERIAL:
                        $typeName = ' (MATERIAL)';
                        break;
                    case RejectGroup::REJECT_PROCESS:
                        $typeName = ' (PROCESS)';
                        break;
                }

                if (!isset($rejectData[$reject->id]))
                    $rejectData[$reject->id] = ['id' => $reject->id, 'reject_group_id' => $reject->reject_group_id, 'name' => $reject->name, 'label' => '_' . $reject->id, 'name_2' => $reject->name . $typeName, 'count' => 0];
            }

            //count reject
            if ($row->reject_summary) {
                $rejectSummary = json_decode($row->reject_summary);
                if ($rejectSummary) {
                    foreach ($rejectSummary as $categoryId => $rejectCategory) {

                        if ($categoryId == RejectGroup::REJECT_SETTING && $rejectCategory->total)
                            $row->reject_setting = $rejectCategory->total;
                        elseif ($categoryId == RejectGroup::REJECT_MATERIAL && $rejectCategory->total)
                            $row->reject_material = $rejectCategory->total;
                        elseif ($categoryId == RejectGroup::REJECT_PROCESS && $rejectCategory->total)
                            $row->reject_process = $rejectCategory->total;


                        foreach ($rejectCategory as $rejectId => $rejectCount) {
                            if (isset($rejectData[$rejectId]))
                                $rejectData[$rejectId]['count'] += $rejectCount;
                        }
                    }
                }
            }
            //-------------

            $partData = json_decode($row->part_data);

            $row->no = ++$no;
            $row->part_no = $partData->part_no;
            $row->part_name = $partData->name;

            $data['total_actual_output'] += $row->actual_output;
            $data['total_reject_count'] += $row->reject_count;

            $data['data'][] = $row;

            unset($row);
        }

        if ($data['total_actual_output'] > 0)
            $data['reject_percentage'] = $data['total_reject_count'] / $data['total_actual_output'];

        $rejectById = [];
        //Remove zeros
        foreach ($rejectData as $key => $value) {
            if (!$value['count']) {
                unset($rejectData[$key]);
                continue;
            }
            $rejectById[$key] = $value;
        }

        //sort by highest
        usort($rejectData, function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });

        //add defect column to rows
        foreach ($rows as &$row) {
            foreach ($rejectData as $key => $reject) {
                $label = $reject['label'];
                $row->$label = 0;
            }

            if ($row->reject_summary) {
                $rejectSummary = json_decode($row->reject_summary);
                if ($rejectSummary) {
                    foreach ($rejectSummary as $categoryId => $rejectCategory) {

                        foreach ($rejectCategory as $rejectId => $rejectCount) {

                            if (isset($rejectById[$rejectId])) {

                                $label = $rejectById[$rejectId]['label'];
                                $row->$label = $rejectCount;
                            }
                        }
                    }
                }
            }

            unset($row);
        }



        $data['defects'] = $rejectData;

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

        $sheet->setCellValue('A' . $row, 'Operational Analysis - Quality');
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

        $sheet->setCellValue('A' . $row, 'Total Actual Output');
        $sheet->setCellValue('B' . $row, $this->data['total_actual_output'] ?? '-');
        $sheet->setCellValue('C' . $row, 'PCS');
        $row++;

        $sheet->setCellValue('A' . $row, 'Total Reject Count');
        $sheet->setCellValue('B' . $row, $this->data['total_reject_count'] ?? '-');
        $sheet->setCellValue('C' . $row, 'PCS');
        $row++;

        $sheet->setCellValue('A' . $row, 'Reject Percentage');
        $sheet->setCellValue('B' . $row, (isset($this->data['reject_percentage']) && is_numeric($this->data['reject_percentage']) ? $this->data['reject_percentage'] * 100 : '-') . '%');
        $row++;

        $row++;


        $shiftText = [
            ShiftType::DAY_SHIFT => 'Day',
            ShiftType::NIGHT_SHIFT => 'Night'
        ];



        $sheet->setCellValue('A' . $row, 'Reject By Defect Part');
        $row++;
        $row++;
        $sheet->setCellValue('A' . $row, 'Reject Type');
        $sheet->setCellValue('B' . $row, 'Count');
        $row++;

        foreach ($this->data['defects'] as $defect) {
            $sheet->setCellValue('A' . $row, $defect['name_2']);
            $sheet->setCellValue('B' . $row, $defect['count']);
            $row++;
        }

        $row++;

        /*
            +"work_center_uid": "r1g"
            +"work_center_name": "R1G"
            +"shift_date": "2022-12-01"
            +"shift_type_id": 1
            +"line_no": 1
            +"order_no": "241106349303"
            +"actual_output": 825
            +"reject_count": 15
            +"reject_summary": "{"1":{"total":2,"2":2},"2":{"total":8,"5":2,"43":6},"3":{"total":5,"9":1,"12":4}}"
            +"part_data": "{"id":13,"plant_id":1,"work_center_id":1,"part_no":"5757A312_","line_no":1,"name":"MOULDING, R\/DR B\/LINE OTR RH","setup_time":900,"cycle_time":22,"packaging": ▶"
            +"started_at": "2022-12-01 16:47:08"
            +"stopped_at": "2022-12-01 23:35:45"
            +"reject_setting": 2
            +"reject_process": 5
            +"reject_material": 8
            +"no": 1
            +"part_no": "5757A312_"
            +"part_name": "MOULDING, R/DR B/LINE OTR RH"
            +"_2": 2
            +"_47": 0
            +"_9": 1
            +"_5": 2
            +"_48": 0
            +"_43": 6
            +"_44": 0
            +"_12": 4
            +"_40": 0
            +"_1": 0
            +"_52": 0
            +"_46": 0
            +"_3": 0
            +"_53": 0
        */
        $sheet->setCellValue('A' . $row, 'No');
        $sheet->setCellValue('B' . $row, 'Date');
        $sheet->setCellValue('C' . $row, 'Shift');
        $sheet->setCellValue('D' . $row, 'Line');
        $sheet->setCellValue('E' . $row, 'Production Order');
        $sheet->setCellValue('F' . $row, 'Part Number');
        $sheet->setCellValue('G' . $row, 'Part Name');
        $sheet->setCellValue('H' . $row, 'Total Output');
        $sheet->setCellValue('I' . $row, 'Total Reject');
        $sheet->setCellValue('J' . $row, 'Setting Reject');
        $sheet->setCellValue('K' . $row, 'Process Reject');
        $sheet->setCellValue('L' . $row, 'Material Reject');
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
                $sheet->setCellValue('H' . $row, $item->actual_output ?? '-');
                $sheet->setCellValue('I' . $row, $item->reject_count ?? '-');
                $sheet->setCellValue('J' . $row, $item->reject_setting ?? '-');
                $sheet->setCellValue('K' . $row, $item->reject_process ?? '-');
                $sheet->setCellValue('L' . $row, $item->reject_material ?? '-');
                $row++;
            }
        }

        $filename = "analysis_quality_" . $workCenter->uid ?? '' . $this->dateStart . '_' . $this->dateEnd;
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
