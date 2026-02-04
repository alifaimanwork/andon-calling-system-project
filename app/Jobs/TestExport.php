<?php

namespace App\Jobs;


use App\Models\Downtime;
use App\Models\DowntimeEvent;
use App\Models\ETTOP10Log;
use App\Models\ETTOP20Log;
use App\Models\Plant;
use App\Models\Production;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TestExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $plantId;
    public $fileType;
    public $logId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($plantUid, $fileType, $logId)
    {
        $plant = Plant::where('uid', $plantUid)->first();

        $this->plantId = $plant->id;
        $this->fileType = $fileType;
        $this->logId = $logId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('TestExport[' . $this->fileType . ':' . $this->logId . '] JOB STARTED');
        $micro = microtime(true);
        /** @var \App\Models\Plant $plant */
        $plant = Plant::find($this->plantId);
        if (!$plant)
            return;

        $plant->loadAppDatabase();
        $connection = $plant->getPlantConnection();


        switch (strtolower($this->fileType)) {
            case 'ett10':
                $logData = ETTOP10Log::on($connection)->find($this->logId);
                break;
            case 'ett20':
                $logData = ETTOP20Log::on($connection)->find($this->logId);
                break;
            default;
                $logData = null;
                break;
        }

        if(!$logData)
            Log::info("Log entry not found!"); 
        else
            Log::info($logData->updateData()->generateContent());

        //Trigger shiftEnded for auto close
        Log::info('TestExport[' . $this->fileType . ':' . $this->logId . '] JOB ENDED ' . (microtime(true) - $micro));
    }
}
