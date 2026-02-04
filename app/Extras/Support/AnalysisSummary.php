<?php

declare(strict_types=1);

namespace App\Extras\Support;

use App\Extras\Interfaces\ExportableReport;
use App\Models\Plant;
use App\Models\Production;
use App\Models\ProductionLine;
use App\Models\WorkCenter;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\DB;
use JsonSerializable;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Stringable;

class AnalysisSummary extends ResultData implements Arrayable, Jsonable, JsonSerializable, Stringable, ExportableReport
{
    public $plant = null;
    public $data = null;
    public $dateStart = null;
    public $dateEnd = null;
    public static function create(Plant $plant, string $dateStart, string $dateEnd)
    {

        $analysis = new AnalysisSummary();

        $rows = DB::connection($plant->onPlantDb()->getPlantConnection())
            ->table(WorkCenter::TABLE_NAME)
            ->join(Production::TABLE_NAME, WorkCenter::TABLE_NAME . '.id', '=', Production::TABLE_NAME . '.work_center_id')
            ->join(ProductionLine::TABLE_NAME, Production::TABLE_NAME . '.id', '=', ProductionLine::TABLE_NAME . '.production_id')

            ->where(WorkCenter::TABLE_NAME . '.plant_id', '=', $plant->id)

            ->where(Production::TABLE_NAME . '.shift_date', '>=', $dateStart)
            ->where(Production::TABLE_NAME . '.shift_date', '<=', $dateEnd)

            ->where(function ($q) {
                $q->where(WorkCenter::TABLE_NAME . '.enabled', '=', 1)
                    ->orWhereNull(WorkCenter::TABLE_NAME . '.id');
            })
            ->where(function ($q) {
                $q->where(Production::TABLE_NAME . '.status', '=', Production::STATUS_STOPPED)
                    ->orWhereNull(Production::TABLE_NAME . '.id');
            })
            ->where(ProductionLine::TABLE_NAME . '.actual_output', '>', 0)
            ->select([
                //ProductionLine::TABLE_NAME. '.id',
                //Production::TABLE_NAME. '.average_oee',
                //ProductionLine::TABLE_NAME. '.oee',
                Production::TABLE_NAME . '.id as production_id',
                Production::TABLE_NAME . '.runtime_summary_cache as runtime_summary',
                ProductionLine::TABLE_NAME . '.oee',
                ProductionLine::TABLE_NAME . '.actual_output',
                ProductionLine::TABLE_NAME . '.reject_count',
                ProductionLine::TABLE_NAME . '.standard_output',
            ])->get();


        $summaryData = [
            'total_standard_output' => 0,
            'total_actual_output' => 0,
            'total_reject_count' => 0,
            'total_downtimes_unplan' => 0,
            'total_runtimes_plan' => 0,
            'average_oee' => 0
        ];

        $productionIds = [];
        foreach ($rows as $row) {
            //Runtime & Downtime
            if (!in_array($row->production_id, $productionIds)) {
                $productionIds[] = $row->production_id;

                if ($row->runtime_summary) {
                    $runtimeSummary = json_decode($row->runtime_summary, true);
                    if ($runtimeSummary) {
                        $summaryData['total_downtimes_unplan'] += $runtimeSummary['downtimes']['unplan']['duration'] ?? 0;
                        $summaryData['total_runtimes_plan'] += $runtimeSummary['runtimes']['plan']['duration'] ?? 0;
                    }
                }
            }

            $summaryData['total_standard_output'] += $row->standard_output;
            $summaryData['total_actual_output'] += $row->actual_output;
            $summaryData['total_reject_count'] += $row->reject_count;
            $summaryData['average_oee'] += $row->oee;
        }
        if (count($rows) > 1) {
            $summaryData['average_oee'] /= count($rows);
            if (count($productionIds) > 1) {
                $summaryData['total_downtimes_unplan'];
                $summaryData['total_runtimes_plan'];
            }
        }



        $analysis->data = $summaryData;
        $analysis->dateStart = $dateStart;
        $analysis->dateEnd = $dateEnd;
        $analysis->plant = $plant;

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
        if (!$plant)
            abort(404);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $row = 1;

        $sheet->setCellValue('A' . $row, 'Operational Analysis - Summary');
        $row++;
        $row++;

        $sheet->setCellValue('A' . $row, 'Generated At');
        $sheet->setCellValue('B' . $row, $plant->getLocalDateTime());
        $row++;

        $sheet->setCellValue('A' . $row, 'Plant');
        $sheet->setCellValue('B' . $row, $plant->name);
        $row++;

        $sheet->setCellValue('A' . $row, 'Date Start');
        $sheet->setCellValue('B' . $row, $this->dateStart);
        $row++;

        $sheet->setCellValue('A' . $row, 'Date End');
        $sheet->setCellValue('B' . $row, $this->dateEnd);
        $row++;

        $row++;

        /* $data
            "total_standard_output" => 0
            "total_actual_output" => 0
            "total_reject_count" => 0
            "total_downtimes_unplan" => 0
            "total_runtimes_plan" => 0
            "average_oee" => 0
        */
        $sheet->setCellValue('A' . $row, 'Total Plan Output');
        $sheet->setCellValue('B' . $row, $this->data['total_standard_output'] ?? '-');
        $sheet->setCellValue('C' . $row, 'PCS');
        $row++;

        $sheet->setCellValue('A' . $row, 'Total Actual Output');
        $sheet->setCellValue('B' . $row, $this->data['total_actual_output'] ?? '-');
        $sheet->setCellValue('C' . $row, 'PCS');
        $row++;

        $sheet->setCellValue('A' . $row, 'Total Reject Part');
        $sheet->setCellValue('B' . $row, $this->data['total_reject_count'] ?? '-');
        $sheet->setCellValue('C' . $row, 'PCS');
        $row++;

        $sheet->setCellValue('A' . $row, 'Total Downtime');
        $sheet->setCellValue('B' . $row, $this->data['total_downtimes_unplan'] ?? '-');
        $sheet->setCellValue('C' . $row, 'HRS');
        $row++;

        $sheet->setCellValue('A' . $row, 'Total Working Hour');
        $sheet->setCellValue('B' . $row, $this->data['total_runtimes_plan'] ?? '-');
        $sheet->setCellValue('C' . $row, 'HRS');
        $row++;

        $sheet->setCellValue('A' . $row, 'Average OEE');
        $sheet->setCellValue('B' . $row, (isset($this->data['average_oee']) && is_numeric($this->data['average_oee'])) ? $this->data['average_oee'] * 100 : '-');
        $sheet->setCellValue('C' . $row, '%');
        $row++;


        $filename = "analysis_summary_" . $this->dateStart . '_' . $this->dateEnd;
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
