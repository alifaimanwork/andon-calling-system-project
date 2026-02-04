<?php

namespace App\Console\Commands;

use App\Extras\Payloads\DowntimeData;
use App\Models\DowntimeType;
use App\Models\OpcActiveTag;
use App\Models\OpcServer;
use App\Models\OpcTag;
use App\Models\Plant;
use App\Models\WorkCenter;
use App\Models\WorkCenterDowntime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TriggerDowntime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trigger:downtime {plant_uid} {work_center_uid} {work_center_downtime_id?} {state=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger downtime for work center';

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
        $workCenterDowntimeId = $this->argument('work_center_downtime_id');
        $state = $this->argument('state') ? 1 : 0;


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
        if ($workCenterDowntimeId)
            $workCenterDowntime = $workCenter->workCenterDowntimes()->with('downtime')->find($workCenterDowntimeId);
        else
            $workCenterDowntime = null;

        $downtimeTypes = DowntimeType::on($workCenter->getConnectionName())->pluck('name', 'id')->toArray();
        $len = 13;
        foreach ($downtimeTypes as $id => $downtimeType) {
            if ($len < strlen($downtimeType))
                $len = strlen($downtimeType);
        }


        if (!$workCenterDowntime) {
            //list all machine downtime for selected workcenter

            $workCenterDowntimes = $workCenter->workCenterDowntimes()->with('downtime')->get();
            if ($workCenterDowntimeId)
                $this->warn('Invalid Work Center Downtime ID');

            $this->warn('Available Machine Downtime for ' . $workCenter->uid . ':');

            $this->warn(sprintf("% 5s : %-" . $len . "s : %s", 'ID', 'Downtime Type', 'Name'));
            foreach ($workCenterDowntimes as $workCenterDowntime) {

                $downtimeType = $downtimeTypes[$workCenterDowntime->downtime->downtime_type_id] ?? '-';
                $this->info(sprintf("% 5d : %-" . $len . "s : %s", $workCenterDowntime->id, $downtimeType, $workCenterDowntime->downtime->category));
            }

            return 0;
        }

        /** @var \App\Models\Production $production */
        $production = $workCenter->currentProduction;
        if (!$production) {
            $this->warn('No production running at ' . $workCenter->uid);
            return;
        }
        $downtimeData = new DowntimeData($workCenterDowntime->id,$workCenterDowntime->downtime_id, $state);
        
        $production->setDowntime($downtimeData);


        return 0;
    }
}
