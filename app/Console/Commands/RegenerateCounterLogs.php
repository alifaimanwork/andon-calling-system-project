<?php

namespace App\Console\Commands;

use App\Models\CounterLog;
use App\Models\OpcActiveTag;
use App\Models\OpcServer;
use App\Models\OpcTag;
use App\Models\OpcTagType;
use App\Models\Plant;
use App\Models\Production;
use App\Models\WorkCenter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class RegenerateCounterLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'regenerate:counter_logs {plant_uid} {production_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear and regenerate counter logs';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //{plant_uid} {work_center_uid} {line_no}

        $plantUid = $this->argument('plant_uid');
        $productionId = $this->argument('production_id');


        /** @var \App\Models\Plant $plant */
        $plant = Plant::where('uid', $plantUid)->first();

        if (!$plant) {
            $this->info("Invalid Plant UID");
            return 0;
        }
        $connection = $plant->onPlantDb()->getPlantConnection();

        /** @var \App\Models\Production $production */
        $production = Production::on($connection)->find($productionId);

        if (!$production) {
            $this->info("Production not found");
            return 0;
        }

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $production->workCenter;
        if (!$workCenter) {
            $this->info("Work Center not found");
            return 0;
        }

        $productionLines = $production->productionLines;

        if (!$this->confirm("Regenerate counter logs for Production ID: " . $production->id . ' WorkCenter: ' . $workCenter->uid))
            return 0;

        /** @var \App\Models\ProductionLine $productionLine */
        foreach ($productionLines as $productionLine) {
            $this->warn("Processing Line " . $productionLine->line_no . '...');

            $opcTag = $workCenter->opcTags()->where('opc_tag_type_id', '=', OpcTagType::TAG_COUNTER)->where('info', '=', $productionLine->line_no)->first();
            if (!$opcTag)
                $this->warn("Counter Tag Not Found... skip line");


            $this->info('Clearing existing count logs');
            DB::connection($connection)->delete('DELETE FROM `' . CounterLog::TABLE_NAME . '` WHERE production_line_id=?', [$productionLine->id]);

            $this->info('Regenerating counter log from tag: ' . $opcTag->tag);
            $query = DB::table('opc_logs')
                ->where('tag', '=', $opcTag->tag)
                ->where('server_id', '=', $opcTag->opc_server_id)
                ->where('created_at', '>=', $production->started_at);

            if ($production->stopped_at)
                $query->where('created_at', '<=', $production->stopped_at);

            $prevTagLog = DB::table('opc_logs')
                ->where('tag', '=', $opcTag->tag)
                ->where('server_id', '=', $opcTag->opc_server_id)
                ->where('created_at', '<', $production->started_at)
                ->orderBy('created_at', 'desc')->first();


            $tagLogs = $query->orderBy('created_at', 'asc')->get();

            $prevValue = 0;
            if ($prevTagLog) {
                if ($prevValue = $prevTagLog->value);
            }


            foreach ($tagLogs as $tagLog) {
                $delta = $tagLog->value - $prevValue;
                if ($delta <= 0) {
                    $prevValue = $tagLog->value;
                    continue;
                }
                
                $this->warn('[' . $tagLog->created_at . '] ' . $delta . ' (' . $prevValue . ' => ' . $tagLog->value . ')');
                $counterLog = new CounterLog();
                $counterLog->work_center_id = $workCenter->id;
                $counterLog->opc_tag_id = $opcTag->id;
                $counterLog->production_line_id = $productionLine->id;
                $counterLog->line_no = $productionLine->line_no;
                $counterLog->count = $delta;
                $counterLog->recorded_at = $tagLog->created_at;
                $counterLog->setConnection($connection)->save();

                $prevValue = $tagLog->value;
            }


            $this->info('Recalculate production line summary...');
            $productionLine->updateActualOutput()->updateOkCount()->save();
        }
        $this->info('Recalculate production summary...');
        $production->updateHourlySummary()->save();
        $this->info('Done');

        return 0;
    }
}
