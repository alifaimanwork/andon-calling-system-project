<?php

declare(strict_types=1);

namespace App\Extras\Support;

use App\Extras\Utils\ExcelTemplate;
use App\Http\Controllers\Web\Analysis\ProductivityController;
use App\Models\Plant;
use App\Models\Production;
use App\Models\ProductionLine;
use App\Models\WorkCenter;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\DB;
use JsonSerializable;
use Stringable;

class AnalysisDpr extends ResultData implements Arrayable, Jsonable, JsonSerializable, Stringable
{
    public \DateTime | null $production_started_at;
    public \DateTimeZone | null $plant_time_zone;
    public string | null $work_center_uid;
    public int |null $production_id;

    public array $data = [];
    public static function create(Plant $plant, Production $production)
    {
        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $production->workCenter;

        $analysis = new AnalysisDpr();

        $localTimeZone = $plant->getLocalDateTimeZone();
        $analysis->production_started_at = $production->started_at;
        $analysis->plant_time_zone = $localTimeZone;
        $analysis->work_center_uid = $workCenter->uid;
        $analysis->production_id = $production->id;

        $productionLines = $production->productionLines;
        $dprData = [];
        $dprData['_production_id'] = $production->id;
        $dprData['_row_count'] = 0;
        $dprData['date'] = $production->started_at->setTimezone($localTimeZone)->format('d/m/Y');
        $dprData['shift'] = $production->shiftType->label;

        $manpower = $production->die_change_info['man_power'] ?? null;
        if (!is_numeric($manpower))
            $manpower = null;
        $dprData['man_power'] = $manpower ?? '-';
        $dprData['work_center'] = $workCenter->name;

        $runtimeSummary = null;
        $goodRuntime = 0;
        if ($production->runtime_summary_cache)
            $runtimeSummary = $production->runtime_summary_cache;

        if ($runtimeSummary) {
            $dprData['total_runtime_plan'] = round(($runtimeSummary['runtimes']['plan']['duration'] ?? 0) / 60, 2); //in minutes
            $dprData['total_runtime_good'] = round(($runtimeSummary['runtimes']['good']['duration'] ?? 0) / 60, 2); //in minutes
            $dprData['total_downtime_plan'] = round(($runtimeSummary['downtimes']['plan']['duration'] ?? 0) / 60, 2); //in minutes
            $dprData['total_downtime_plan_die_change'] = round(($runtimeSummary['downtimes']['plan_die_change']['duration'] ?? 0) / 60, 2); //in minutes
            $dprData['total_downtime_plan_break'] = round(($runtimeSummary['downtimes']['plan_break']['duration'] ?? 0) / 60, 2); //in minutes

            $dprData['total_downtime_unplan_die_change'] = round(($runtimeSummary['downtimes']['unplan_die_change']['duration'] ?? 0) / 60, 2); //in minutes
            $dprData['total_downtime_unplan_machine'] = round(($runtimeSummary['downtimes']['unplan_machine']['duration'] ?? 0) / 60, 2); //in minutes
            $dprData['total_downtime_unplan_human'] = round(($runtimeSummary['downtimes']['unplan_human']['duration'] ?? 0) / 60, 2); //in minutes
            $dprData['total_downtime_unplan'] = round(($runtimeSummary['downtimes']['unplan']['duration'] ?? 0) / 60, 2); //in minutes



            $goodRuntime = ($runtimeSummary['runtimes']['good']['duration'] ?? 0);
        }

        //dd($production->started_at->format('Y-m-d H:i:s'),$production->stopped_at->format('Y-m-d H:i:s'),$runtimeSummary['runtimes']['good'],$productionLines[0]->part_data);//->cycle_time);

        if (isset($production->schedule_data['shift_start_end']['over_time'])) {
            $overTime = new \DateTime($production->schedule_data['shift_start_end']['over_time']);
            if (isValueInsideRange($overTime, $production->started_at, $production->stopped_at)) {
                $dprData['overtime_start'] = $overTime->setTimezone($localTimeZone)->format('Y-m-d H:i:s');
                $dprData['overtime_end'] = $production->stopped_at->setTimezone($localTimeZone)->format('Y-m-d H:i:s');
            } else {
                $dprData['overtime_start'] = '-';
                $dprData['overtime_end'] = '-';
            }
        }




        // $dprData['total_runtime_good'] = round($goodRuntime / 60, 2); //in minutes


        for ($n = 0; $n < $production->die_change_info['lot_count']; $n++) {
            $dprData['die_change_info_lot_' . ($n + 1) . '_coil_bar'] = $production->die_change_info['coil_bar'][$n] ?? '';
            $dprData['die_change_info_lot_' . ($n + 1) . '_child_part'] = $production->die_change_info['child_part'][$n] ?? '';
            $dprData['die_change_info_lot_' . ($n + 1) . '_material_part'] = $production->die_change_info['material_part'][$n] ?? '';
        }

        $blockKeys = []; {
            $hourlySummary = $production->hourly_summary;
            foreach ($hourlySummary as $block) {
                $blocks[] = $block;
            }
            usort($blocks, function ($a, $b) {
                return $a['start'] <=> $b['start'];
            });

            for ($i = 0; $i < count($blocks); $i++) {
                $dprData['_row_count']++;
                $block = $blocks[$i];
                $localStartTime = (new \DateTime($block['local_start']));
                $blockKeys[] = $localStartTime->format('H');

                $dprData['row_' . $i . '_start_time'] = $localStartTime->format('H:i');
                $dprData['row_' . $i . '_end_time'] = (new \DateTime($block['local_end']))->format('H:i');

                $dprData['row_' . $i . '_total_runtime_plan'] = round(($block['runtime_summary']['runtimes']['plan']['duration'] ?? 0) / 60, 2); //in minutes

                $dprData['row_' . $i . '_total_downtime_plan_die_change'] = round(($block['runtime_summary']['downtimes']['plan_die_change']['duration'] ?? 0) / 60, 2); //in minutes
                $dprData['row_' . $i . '_total_downtime_plan_break'] = round(($block['runtime_summary']['downtimes']['plan_break']['duration'] ?? 0) / 60, 2); //in minutes
                $dprData['row_' . $i . '_total_downtime_plan'] = round(($block['runtime_summary']['downtimes']['plan']['duration'] ?? 0) / 60, 2); //in minutes


                $dprData['row_' . $i . '_total_downtime_unplan_die_change'] = round(($block['runtime_summary']['downtimes']['unplan_die_change']['duration'] ?? 0) / 60, 2); //in minutes
                $dprData['row_' . $i . '_total_downtime_unplan_machine'] = round(($block['runtime_summary']['downtimes']['unplan_machine']['duration'] ?? 0) / 60, 2); //in minutes
                $dprData['row_' . $i . '_total_downtime_unplan_human'] = round(($block['runtime_summary']['downtimes']['unplan_human']['duration'] ?? 0) / 60, 2); //in minutes
                $dprData['row_' . $i . '_total_downtime_unplan'] = round(($block['runtime_summary']['downtimes']['unplan']['duration'] ?? 0) / 60, 2); //in minutes

                $dprData['row_' . $i . '_total_downtime_unplan_die_change_accumulate'] = round(($block['runtime_summary_accumulate']['downtimes']['unplan_die_change']['duration'] ?? 0) / 60, 2); //in minutes
                $dprData['row_' . $i . '_total_downtime_unplan_machine_accumulate'] = round(($block['runtime_summary_accumulate']['downtimes']['unplan_machine']['duration'] ?? 0) / 60, 2); //in minutes
                $dprData['row_' . $i . '_total_downtime_unplan_human_accumulate'] = round(($block['runtime_summary_accumulate']['downtimes']['unplan_human']['duration'] ?? 0) / 60, 2); //in minutes
                $dprData['row_' . $i . '_total_downtime_unplan_accumulate'] = round(($block['runtime_summary_accumulate']['downtimes']['unplan']['duration'] ?? 0) / 60, 2); //in minutes

                $dprData['row_' . $i . '_total_runtime_good'] = round(($block['runtime_summary']['runtimes']['good']['duration'] ?? 0) / 60, 2); //in minutes
                $dprData['row_' . $i . '_total_runtime_good_accumulate'] = round(($block['runtime_summary_accumulate']['runtimes']['good']['duration'] ?? 0) / 60, 2); //in minutes



            }
        }
        //dd($dprData);
        /** @var \App\Models\ProductionLine $productionLine */
        foreach ($productionLines as $productionLine) {
            /** @var \App\Models\ProductionOrder $productionOrder */
            $productionOrder = $productionLine->productionOrder;
            $partData = $productionLine->part_data;

            $overallSummary = $productionLine->overall_summary;
            $rejectSummary = $productionLine->reject_summary;
            $hourlySummary = $productionLine->hourly_summary;

            $dprData['line_' . $productionLine->line_no . '_total_downtime_unplan'] = $dprData['total_downtime_unplan'];
            $dprData['line_' . $productionLine->line_no . '_total_runtime_plan'] = $dprData['total_runtime_plan'];
            $dprData['line_' . $productionLine->line_no . '_total_runtime_good'] = $dprData['total_runtime_good'];





            $dprData['line_' . $productionLine->line_no . '_production_order_no'] = $productionOrder->order_no;
            $dprData['line_' . $productionLine->line_no . '_part_no'] = $partData['part_no'];
            $dprData['line_' . $productionLine->line_no . '_part_name'] = $partData['name'];
            $dprData['line_' . $productionLine->line_no . '_part_cycle_time'] = $partData['cycle_time'];
            $dprData['line_' . $productionLine->line_no . '_plan_die_change'] = $partData['setup_time'];

            $dprData['line_' . $productionLine->line_no . '_ok_count'] = $productionLine->ok_count;
            $dprData['line_' . $productionLine->line_no . '_reject_count'] = $productionLine->reject_count;
            $dprData['line_' . $productionLine->line_no . '_actual_output'] = $productionLine->actual_output;
            $dprData['line_' . $productionLine->line_no . '_standard_output'] = $overallSummary['standard_output'];

            if ($goodRuntime > 0)
                $outputRate = $productionLine->actual_output / ($goodRuntime / 3600); //actual_output / runtime (pcs/hour)
            else
                $outputRate  = $productionLine->actual_output > 0 ? null : 0;

            $dprData['line_' . $productionLine->line_no . '_output_rate'] = round($outputRate, 2) ?? '-';

            if ($manpower && $manpower > 0)
                $dprData['line_' . $productionLine->line_no . '_output_manhour_rate'] = round($outputRate / $manpower, 2);
            else
                $dprData['line_' . $productionLine->line_no . '_output_manhour_rate'] = '-';

            if ($productionLine->actual_output > 0)
                $dprData['line_' . $productionLine->line_no . '_rejection'] = round($productionLine->reject_count / $productionLine->actual_output * 100) . '%';
            else
                $dprData['line_' . $productionLine->line_no . '_rejection'] = '0%';

            if ($partData['reject_target'] && is_numeric($partData['reject_target']))
                $dprData['line_' . $productionLine->line_no . '_reject_target'] = round($partData['reject_target'] * 100) . '%';
            else
                $dprData['line_' . $productionLine->line_no . '_reject_target'] = '0%';


            $dprData['line_' . $productionLine->line_no . '_part_no'] = $partData['part_no'];


            $dprData['line_' . $productionLine->line_no . '_availability'] = round($overallSummary['availability'] * 100) . '%';
            $dprData['line_' . $productionLine->line_no . '_performance'] = round($overallSummary['performance'] * 100) . '%';
            $dprData['line_' . $productionLine->line_no . '_quality'] = round($overallSummary['quality'] * 100) . '%';
            $dprData['line_' . $productionLine->line_no . '_oee'] = round($overallSummary['oee'] * 100) . '%';


            for ($i = 1; $i <= 3; $i++) {
                $dprData['line_' . $productionLine->line_no . '_reject_' . $i . '_total'] = $rejectSummary[$i]['total'] ?? 0;
                //1: Reject type setting
                //2: Reject type material
                //3: Reject type process
            }

            //TODO: count & accumulate count block row, accumulate downtime
            //block count

            $blocks = [];

            foreach ($hourlySummary as $block) {
                $blocks[] = $block;
            }
            usort($blocks, function ($a, $b) {
                return $a['start'] <=> $b['start'];
            });

            for ($n = 0; $n < count($blocks); $n++) {
                $block = $blocks[$n];

                $dprData['line_' . $productionLine->line_no . '_row_' . $n . '_total_runtime_good'] = $dprData['row_' . $n . '_total_runtime_good'];
                $dprData['line_' . $productionLine->line_no . '_row_' . $n . '_total_runtime_good_accumulate'] = $dprData['row_' . $n . '_total_runtime_good_accumulate'];;


                $dprData['line_' . $productionLine->line_no . '_row_' . $n . '_standard_output'] = $block['standard_output'] ?? 0; //Standard output
                $dprData['line_' . $productionLine->line_no . '_row_' . $n . '_standard_output_accumulate'] = $block['standard_output_accumulate'] ?? 0; //Standard output accumulate

                $dprData['line_' . $productionLine->line_no . '_row_' . $n . '_actual_output'] = $block['actual_output'] ?? 0; //actual output accumulate
                $dprData['line_' . $productionLine->line_no . '_row_' . $n . '_actual_output_accumulate'] = $block['actual_output_accumulate'] ?? 0; //actual output accumulate

                $dprData['line_' . $productionLine->line_no . '_row_' . $n . '_standard_output'] = $block['standard_output'] ?? 0; //Standard output


                for ($i = 1; $i <= 3; $i++) {
                    $dprData['line_' . $productionLine->line_no  . '_row_' . $n . '_reject_' . $i . '_total'] = $block['reject_summary'][$i]['total'] ?? 0;
                    $dprData['line_' . $productionLine->line_no  . '_row_' . $n . '_reject_' . $i . '_total_accumulate'] = $block['reject_summary_accumulate'][$i]['total'] ?? 0;
                    //1: Reject type setting
                    //2: Reject type material
                    //3: Reject type process
                }
            }
        }

        $analysis->data = $dprData;

        return $analysis;
    }


    public function exportExcel()
    {
        $xlsTemplate = new ExcelTemplate('storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'dpr_templates' . DIRECTORY_SEPARATOR . 'dpr_smi.xlsx', $this->data);
        $spreadsheet = $xlsTemplate->render();
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        //plant time
        $local_start_at = $this->production_started_at->setTimezone($this->plant_time_zone);

        // "dpr_" . $date('start_at') . "_" . $work_center_uid . "_" . $production_id . ".xlsx";
        $fileName = "DPR_" . $local_start_at->format('YmdHi') . "_" . strtoupper($this->work_center_uid) . "_" . $this->production_id . ".xlsx";

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
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
}
