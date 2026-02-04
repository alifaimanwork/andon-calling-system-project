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

class ProcessProductionClosing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $plantId;
    public $productionId;
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
        Log::info('ProcessProductionClosing[' . $this->productionId . '] JOB STARTED');
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

        

        $production->endOfProductionProcess();
        Log::info('ProcessProductionClosing[' . $production->id . '] JOB ENDED: ' . (microtime(true) - $micro));
        //Trigger shiftEnded for auto close
    }
}
