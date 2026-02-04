<?php

namespace App\Jobs;

use App\Models\Downtime;
use App\Models\DowntimeEvent;
use App\Models\OpcTagType;
use App\Models\Plant;
use App\Models\Production;
use App\Models\WorkCenter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LastCycleStopSignal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $plantId;
    public $productionId;
    public $workCenterId;
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(WorkCenter $workCenter, Production $production)
    {
        $this->plantId = $workCenter->plant->id;
        $this->workCenterId = $workCenter->id;
        $this->productionId = $production->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('LastCycleStopSignal[' . $this->productionId . '] JOB STARTED');
        $micro = microtime(true);
        /** @var \App\Models\Plant $plant */
        $plant = Plant::find($this->plantId);
        if (!$plant)
            return;

        $plant->loadAppDatabase();

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $plant->onPlantDb()->workCenters()->find($this->workCenterId);
        if (!$workCenter)
            return;

        if ($workCenter->current_production_id != $this->productionId)
            return; //target production already ended, do not trigger

        /** @var \App\Models\Production $production */
        $production = $workCenter->currentProduction;
        if (!$production)
            return; //no production running

        $workCenter->sendToOpc(OpcTagType::TAG_ON_PRODUCTION, 0);


        Log::info('LastCycleStopSignal[' . $production->id . '] JOB ENDED: ' . (microtime(true) - $micro));
    }
}
