<?php

namespace App\Console\Commands;

use App\Models\OpcActiveTag;
use App\Models\OpcServer;
use App\Models\OpcTag;
use App\Models\Plant;
use App\Models\WorkCenter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TriggerCountUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trigger:countup {plant_uid} {work_center_uid} {line_no} {count=1}';

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
        //{plant_uid} {work_center_uid} {line_no}

        $plantUid = $this->argument('plant_uid');
        $workCenterUid = $this->argument('work_center_uid');
        $lineNo = $this->argument('line_no');
        $count = $this->argument('count');

        if (!is_numeric($count) || $count < 1) {
            $this->info("Invalid Count");
            return 0;
        }

        /** @var \App\Models\Plant $plant */
        $plant = Plant::where('uid', $plantUid)->first();

        if (!$plant) {
            $this->info("Invalid Plant UID");
            return 0;
        }

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $plant->onPlantDb()->workCenters()->where(WorkCenter::TABLE_NAME . '.uid', $workCenterUid)->first();
        if (!$workCenter) {
            $this->info("Invalid Work Center");
            return 0;
        }



        if ($lineNo <= 0 || $lineNo > $workCenter->production_line_count) {
            $this->info("Invalid Line No");
            return 0;
        }


        /** @var \App\Models\Production $production */
        $production = $workCenter->currentProduction;
        if (!$production) {
            $this->info("No production running");
            return 0;
        }

        /** @var \App\Models\ProductionLine $productionLine */
        $productionLine = $production->productionLines()->where('line_no', $lineNo)->first();
        if (!$productionLine) {
            $this->info("No production running on Line " . $lineNo);
            return 0;
        }

        //dummy opc tag
        $opcTag = new OpcTag();
        $opcTag->setConnection($plant->getPlantConnection());
        $opcTag->work_center_id = $workCenter->id;
        $opcTag->info = $lineNo;
        $opcTag->prev_value = 0;
        $opcTag->value = $count;
        $opcTag->value_updated_at = date('Y-m-d H:i:s');
        $opcTag->generateCountUpEvent();


        return 0;
    }
}
