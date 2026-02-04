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
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class HourlyProductionUpdate implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $issuedAt;
    public $plantId;
    public $productionId;
    public $scheduled;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Plant $plant, Production $production, $scheduled = true, $issuedAt = null)
    {
        if (!$issuedAt)
            $issuedAt = time();

        $this->issuedAt = $issuedAt;

        $this->plantId = $plant->id;
        $this->productionId = $production->id;
        $this->scheduled =  $scheduled;
    }

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 180;

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->plantId . '-' . $this->productionId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('HourlyProductionUpdate[' . $this->productionId . '] JOB STARTED');
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

        if (!$production->last_hourly_production_update || $production->last_hourly_production_update < $this->issuedAt) {
            $production->last_hourly_production_update = time();
            $production->updateHourlySummary()->save();

            //broadcast
            /** @var \App\Models\WorkCenter $workCenter */
            $workCenter = $production->workCenter;
            if ($workCenter && $workCenter->current_production_id == $production->id)
                $workCenter->broadcastWorkCenterDataUpdate();
        }

        if ($this->scheduled) {
            $nextScheduleTime = self::getNextScheduleTime($plant, $production);

            if ($nextScheduleTime)
                dispatch(new HourlyProductionUpdate($plant, $production, true, $nextScheduleTime->getTimestamp()))->delay($nextScheduleTime);
        }

        Log::info('HourlyProductionUpdate[' . $production->id . '] JOB ENDED: ' . (microtime(true) - $micro));
    }
    public static function getNextScheduleTime(Plant $plant, Production $production): \DateTimeInterface | null
    {
        //hourly update follow local time
        $localNow = $plant->getLocalDateTime();
        $localTimeZone = $plant->getLocalDateTimeZone();

        $scheduleNext = \DateTime::createFromFormat('Y-m-d H:i:s', $localNow->format('Y-m-d H:' . '00:00'), $localTimeZone);
        if ($production->stopped_at && $scheduleNext > $production->stopped_at)
            return null;

        $scheduleNext->add(new \DateInterval('PT1H'));
        if ($production->stopped_at && $scheduleNext > $production->stopped_at)
            $scheduleNext = $production->stopped_at;

        if ($scheduleNext->getTimestamp() <= $localNow->getTimestamp())
            return null;

        return $scheduleNext;
    }
}
