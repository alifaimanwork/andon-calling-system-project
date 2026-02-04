<?php

namespace App\Jobs;

use App\Models\Plant;
use App\Models\WorkCenter;
use App\Models\ProductionLine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProductionLineCountUpdate implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $plantId;
    public $workCenterId;
    public $productionLineId;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 60;

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->plantId . '-' . $this->productionLineId;
    }
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Plant $plant, WorkCenter $workCenter, ProductionLine $productionLine)
    {
        $this->plantId = $plant->id;
        $this->workCenterId = $workCenter->id;
        $this->productionLineId = $productionLine->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('ProductionLineCountUpdate[' . $this->productionLineId . '] JOB STARTED');
        $micro = microtime(true);
        /** @var \App\Models\Plant $plant */
        $plant = Plant::find($this->plantId);
        if (!$plant)
            return;

        $plant->loadAppDatabase();
        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $plant->onPlantDb()->workCenters()->where('id', $this->workCenterId)->first();
        if (!$workCenter)
            return;

        /** @var \App\Models\ProductionLine $productionLine */
        $productionLine = ProductionLine::on($plant->getPlantConnection())->find($this->productionLineId);
        if (!$productionLine)
            return;

        /** @var \App\Models\Production */
        $production = $productionLine->production;
        if (!$production)
            return;

        //$productionLine->updateActualOutput()->updateOkCount()->updateHourlySummary()->save();
        $productionLine->recalculateTagCount()->updateActualOutput()->updateOkCount()->updateHourlySummary()->save();
        if ($workCenter->status == WorkCenter::STATUS_RUNNING)
            $productionLine->exportPendingGROK($production->user); //only start export when started production

        /** @var \App\Models\ProductionOrder $productionOrder */
        $productionOrder = $productionLine->productionOrder;

        if ($productionOrder)
            $productionOrder->updateActualOutput()->updateOkCount()->save();

        //Broadcast workcenter data updated
        $workCenter->broadcastWorkCenterDataUpdate();

        //Trigger shiftEnded for auto close
        Log::info('ProductionLineCountUpdate[' . $productionLine->id . '] JOB ENDED: ' . (microtime(true) - $micro));
    }
}
