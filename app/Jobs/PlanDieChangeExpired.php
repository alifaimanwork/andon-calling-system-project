<?php

namespace App\Jobs;

use App\Models\Downtime;
use App\Models\DowntimeEvent;
use App\Models\Plant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PlanDieChangeExpired implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $plantId;
    public $downtimeEventId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Plant $plant, DowntimeEvent $downtimeEvent)
    {
        $this->plantId = $plant->id;
        $this->downtimeEventId = $downtimeEvent->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('PlanDieChangeExpired[' . $this->downtimeEventId . '] JOB STARTED');
        $micro = microtime(true);
        /** @var \App\Models\Plant $plant */
        $plant = Plant::find($this->plantId);
        if (!$plant)
            return;

        $plant->loadAppDatabase();

        /** @var \App\Models\DowntimeEvent $downtimeEvent */
        $downtimeEvent = DowntimeEvent::on($plant->getPlantConnection())->find($this->downtimeEventId);
        if (!$downtimeEvent)
            return;

        $downtimeEvent->planDieChangeExpireEvent();
        Log::info('PlanDieChangeExpired[' . $downtimeEvent->id . '] JOB ENDED: ' . (microtime(true) - $micro));
    }
}
