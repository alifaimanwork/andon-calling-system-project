<?php

namespace App\Console\Commands;

use App\Models\OpcActiveTag;
use App\Models\OpcServer;
use App\Models\OpcTag;
use App\Models\Plant;
use App\Models\Production;
use App\Models\WorkCenter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class RecalculateSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recalculate_summary {plant_uid} {work_center_uid?} {production_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger count up for work center line';

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
        //{plant_uid} {work_center_uid?} {production_id?}

        $plantUid = $this->argument('plant_uid');
        $workCenterUid = $this->argument('work_center_uid');
        $productionId = $this->argument('production_id');

        if (!$this->confirm("Recalculate end of production summary? (" . $plantUid . ")"))
            return 0;

        /** @var \App\Models\Plant $plant */
        $plant = Plant::where('uid', $plantUid)->first();


        if (!$plant) {
            $this->info("Invalid Plant UID");
            return 0;
        }

        if ($workCenterUid) {

            $workCenters = $plant->onPlantDb()->workCenters()->where(WorkCenter::TABLE_NAME . '.uid', $workCenterUid)->get();
            if (!$workCenters) {
                $this->info("Invalid Work Center");
                return 0;
            }
        } else {
            $workCenters = $plant->onPlantDb()->workCenters()->get();
        }

        for ($i = 0; $i < count($workCenters); $i++) {
            /** @var \App\Models\WorkCenter $workCenter */
            $workCenter = $workCenters[$i];

            $this->warn('[' . ($i + 1) . '/' . count($workCenters) . '] ' . $workCenter->uid);

            if ($productionId)
                $productions = $workCenter->productions()->where(Production::TABLE_NAME . '.status', Production::STATUS_STOPPED)->where(Production::TABLE_NAME . '.id', $productionId)->get();
            else
                $productions = $workCenter->productions()->where(Production::TABLE_NAME . '.status', Production::STATUS_STOPPED)->get();

            for ($n = 0; $n < count($productions); $n++) {

                /** @var \App\Models\Production $production */
                $production = $productions[$n];
                $micro = microtime(true);
                $production->endOfProductionProcess();
                $this->info('  [' . ($n + 1) . '/' . count($productions) . '] ' . $production->id . ': ' . $production->shift_date . ' (' . $production->shift_type_id . '): ' . (microtime(true) - $micro));
            }
        }

        return 0;
    }
}
