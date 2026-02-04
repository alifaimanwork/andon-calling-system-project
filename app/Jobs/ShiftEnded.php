<?php

namespace App\Jobs;

use App\Models\Downtime;
use App\Models\DowntimeEvent;
use App\Models\Plant;
use App\Models\Production;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ShiftEnded implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $plantId;
    public $productionId;
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
    public function __construct(Plant $plant, Production $production)
    {
        $this->plantId = $plant->id;
        $this->productionId = $production->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('ShiftEnded[' . $this->productionId . '] JOB STARTED');
        $micro = microtime(true);
        /** @var \App\Models\Plant $plant */
        $plant = Plant::find($this->plantId);
        if (!$plant)
            return;

        $plant->loadAppDatabase();

        /** @var \App\Models\Production $production */
        $production = Production::on($plant->getPlantConnection())->find($this->productionId);
        if (!$production)
            return;

        $production->shiftEnded();
        //Trigger shiftEnded for auto close
        Log::info('ShiftEnded[' . $production->id . '] JOB ENDED: ' . (microtime(true) - $micro));
    }
}
