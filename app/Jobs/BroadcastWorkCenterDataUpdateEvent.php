<?php

namespace App\Jobs;

use App\Events\Terminal\WorkCenterDataUpdateEvent;
use App\Models\Downtime;
use App\Models\DowntimeEvent;
use App\Models\Plant;
use App\Models\WorkCenter;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BroadcastWorkCenterDataUpdateEvent implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $plantId;
    public $workCenterId;
    public $issuedAt;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(WorkCenter $workCenter, $issuedAt = null)
    {


        $plant = $workCenter->plant;
        if (!$plant)
            return;

        if (!$issuedAt)
            $issuedAt = time();

        $this->issuedAt = $issuedAt;
        $this->plantId = $workCenter->plant->id;
        $this->workCenterId = $workCenter->id;
    }

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
        return $this->plantId . '-' . $this->workCenterId;
    }

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

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

        if ($workCenter->last_broadcast_update && $workCenter->last_broadcast_update > $this->issuedAt)
            return;

        Log::info('BroadcastWorkCenterDataUpdateEvent[' . $this->workCenterId . '] JOB STARTED');

        $workCenter->last_broadcast_update = time();
        $workCenter->save();

        try {
            event(new WorkCenterDataUpdateEvent($workCenter));
        } catch (Exception $ex) {
        }

        Log::info('BroadcastWorkCenterDataUpdateEvent[' .  $this->workCenterId . '] JOB ENDED: ' . (microtime(true) - $micro));
    }
}
