<?php

namespace App\Models;


use App\Jobs\DowntimeEventEnded;
use App\Jobs\DowntimeEventStarted;
use App\Jobs\PlanDieChangeExpired;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use stdClass;

/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned integer
 * 
 * @property int $production_id Foreign Key (Production): unsigned integer
 * @property int $downtime_id Foreign Key (Downtime): unsigned integer
 * @property int $user_id Foreign Key (User): unsigned integer
 * 
 * @property int $event_type tiny integer *follow Workcenter downtime state
 * @property string $reason string
 * @property string $user_input_reason string
 * @property int $broadcast_status tiny Integer // 0: Not broadcast, 1: start_time broadcasted, 2: end_time broadcasted
 * @property \Carbon\Carbon $start_time timestamp
 * @property \Carbon\Carbon $end_time timestamp
 * @property \Carbon\Carbon $created_at timestamp
 * @property \Carbon\Carbon $updated_at timestamp
 */
class DowntimeEvent extends Model
{
    const TABLE_NAME = 'downtime_events';
    protected $table = self::TABLE_NAME;

    public $start_timestamp_cache = null;
    public $end_timestamp_cache = null;

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];

    public static function getPriorityMap()
    {
        return [
            WorkCenter::DOWNTIME_STATUS_PLAN_BREAK => 0,
            WorkCenter::DOWNTIME_STATUS_PLAN_DIE_CHANGE => 1, //less = higher priority
            WorkCenter::DOWNTIME_STATUS_UNPLAN_DIE_CHANGE => 2,
            WorkCenter::DOWNTIME_STATUS_UNPLAN_MACHINE => 3,
            WorkCenter::DOWNTIME_STATUS_UNPLAN_HUMAN => 4,
            WorkCenter::DOWNTIME_STATUS_NONE => 99
        ];
    }

    public static function summarize(array $downtimeEvents, array|null $activePeriod = null)
    {
        $states = [];
        //$statesOld = [];
        $eventStartEnd = [];
        $now = new \DateTime();
        $occurance = 0;

        foreach ($downtimeEvents as $downtimeEvent) {
            if (!$downtimeEvent->start_time)
                continue;

            $startTime = $downtimeEvent->start_time->getTimestamp();

            if ($downtimeEvent->end_time && $downtimeEvent->end_time <= $now)
                $endTime = $downtimeEvent->end_time->getTimestamp();
            else
                $endTime = null;



            if (is_null($activePeriod)) {
                $eventStartEnd[] = [$startTime, $endTime];
                $occurance++;
                continue;
            }

            $downtimeHit = false;

            foreach ($activePeriod as $activeTime) {
                $activeStart = $activeTime[0];
                $activeEnd = $activeTime[1];

                if ($activeEnd && $endTime) {
                    if (($startTime > $activeStart && $startTime < $activeEnd) ||
                        ($endTime > $activeStart && $endTime < $activeEnd) ||
                        ($activeStart > $startTime && $activeStart < $endTime) ||
                        ($activeEnd > $startTime && $activeEnd < $endTime) ||
                        ($startTime == $activeStart && $endTime == $activeEnd)
                    ) {

                        //has overlap, define block
                        $newStart = $startTime > $activeStart ? $startTime : $activeStart;
                        $newEnd = $endTime < $activeEnd ? $endTime : $activeEnd;
                        $eventStartEnd[] = [$newStart, $newEnd];
                        $downtimeHit = true;
                    }
                } elseif ($activeEnd && !$endTime) {
                    if (($startTime > $activeStart && $startTime < $activeEnd) ||
                        ($activeStart > $startTime) ||
                        ($activeEnd > $startTime) ||
                        ($startTime == $activeStart)
                    ) {

                        //has overlap, define block
                        $newStart = $startTime > $activeStart ? $startTime : $activeStart;
                        $newEnd = $activeEnd;
                        $eventStartEnd[] = [$newStart, $newEnd];
                        $downtimeHit = true;
                    }
                } elseif (!$activeEnd && $endTime) {
                    if (($startTime > $activeStart) ||
                        ($endTime > $activeStart) ||
                        ($activeStart > $startTime && $activeStart < $endTime) ||
                        ($startTime == $activeStart)
                    ) {
                        //has overlap, define block
                        $newStart = $startTime > $activeStart ? $startTime : $activeStart;
                        $newEnd = $endTime;
                        $eventStartEnd[] = [$newStart, $newEnd];
                        $downtimeHit = true;
                    }
                } else {
                    if ($startTime > $activeStart || $activeStart > $startTime || $startTime == $activeStart) {
                        //has overlap, define block
                        $newStart = $startTime > $activeStart ? $startTime : $activeStart;
                        $newEnd = null;
                        $eventStartEnd[] = [$newStart, $newEnd];
                        $downtimeHit = true;
                    }
                }
            }
            if ($downtimeHit)
                $occurance++;
        }




        foreach ($eventStartEnd as $startEndPair) {
            if ($startEndPair[1] && $startEndPair[1] <= $startEndPair[0])
                continue;

            $states[] = [$startEndPair[0], 1];

            if ($startEndPair[1])
                $states[] = [$startEndPair[1], 0];
        }

        // /** @var \App\Models\DowntimeEvent $downtimeEvent */
        // foreach ($downtimeEvents as $downtimeEvent) {
        //     if ($downtimeEvent->end_time && $downtimeEvent->end_time <= $downtimeEvent->start_time)
        //         continue;

        //     $statesOld[] = [\DateTime::createFromFormat('Y-m-d H:i:s', $downtimeEvent->start_time)->getTimestamp(), 1];

        //     if ($downtimeEvent->end_time)
        //         $statesOld[] = [\DateTime::createFromFormat('Y-m-d H:i:s', $downtimeEvent->end_time)->getTimestamp(), 0];
        // }




        usort($states, function ($a, $b) {
            if ($a[0] == $b[0])
                return $a[1] - $b[1];

            return $a[0] - $b[0];
        });



        $count = 0;
        $start = [];
        $end = [];

        foreach ($states as $state) {
            $prevCount = $count;

            if ($state[1] > 0)
                $count++;
            else
                $count--;

            if ($prevCount == 0 && $count == 1)
                $start[] = $state[0];
            elseif ($count == 0 && $prevCount == 1)
                $end[] = $state[0];
        }
        $nowTimeStamp = $now->getTimestamp();

        if ($count) //has ongoing downtime
            $end[] = $nowTimeStamp;
        if (count($start) != count($end))
            return null;

        $duration = 0;
        for ($n = 0; $n < count($start); $n++) {
            $duration += $end[$n] - $start[$n];
        }

        $ongoing = ($count > 0) ? $nowTimeStamp : null;
        $downtimeEventSummary =  [];

        $downtimeEventSummary['duration'] = $duration;
        $downtimeEventSummary['ongoing'] = $ongoing;
        $downtimeEventSummary['occurance'] = $occurance;

        return $downtimeEventSummary;
    }
    public static function sortByPriority(DowntimeEvent $a, DowntimeEvent $b)
    {


        if ($a->event_type == $b->event_type)
            return 0;

        $priorityMap = DowntimeEvent::getPriorityMap();

        $aValid = isset($priorityMap[$a->event_type]);
        $bValid = isset($priorityMap[$b->event_type]);

        if (!$aValid && !$bValid)
            return 0;
        elseif (!$aValid)
            return -1;
        elseif (!$bValid)
            return 1;

        return $priorityMap[$a->event_type] < $priorityMap[$b->event_type] ? -1 : 1;
    }




    public function getDuration(\DateTime | null $timeEnd = null)
    {
        $timeStart = \DateTime::createFromFormat('Y-m-d H:i:s', $this->start_time);
        if ($this->end_time)
            $timeEnd = \DateTime::createFromFormat('Y-m-d H:i:s', $this->end_time);
        elseif (!$timeEnd)
            $timeEnd = new \DateTime();

        return $timeEnd->getTimestamp() - $timeStart->getTimestamp();
    }

    public function endEvent(\DateTime $closeTime = null)
    {
        if (!$closeTime)
            $closeTime = new \DateTime();
        $this->end_time = $closeTime;

        return $this;
    }

    public function dispatchPlanDieChangeExpireEvent()
    {
        if (!$this->end_time) //delay dispatch only with end time
            return $this;

        /** @var \App\Models\Production $production */
        $production = $this->production;
        if (!$production)
            return $this;

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $production->workCenter;
        if (!$workCenter)
            return $this;

        /** @var \App\Models\Plant $plant */
        $plant = $workCenter->plant;
        if (!$plant)
            return $this;

        PlanDieChangeExpired::dispatch($plant, $this)->delay($this->end_time);
        return $this;
    }
    public function planDieChangeExpireEvent()
    {
        if (
            $this->broadcast_status >= 2 || //event already broadcasted
            !$this->end_time || //invalid end time
            $this->end_time > new \DateTime() //event not ended yet
        )
            return $this;

        //Mark end time event as broadcasted
        $this->broadcast_status = 2;
        $this->save();

        /** @var \App\Models\Production $production */
        $production = $this->production;
        if (!$production)
            return $this;

        $this->processDieChangeEvent();

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $production->workCenter;
        if (!$workCenter)
            return $this;

        //update work center downtime state & force broadcast WorkCenterDataUpdated
        $production->updateHourlySummary()->save();
        $workCenter->updateDowntimeState(true);
        return $this;
    }

    public function processDieChangeEvent()
    {
        /** @var \App\Models\Production $production */
        $production = $this->production;
        if (!$production)
            return $this;

        if (!$production->die_change_end_at || $production->die_change_end_at > $this->end_time) {
            //exceed setup time
            $unplanned = DowntimeEvent::on($this->connection)
                ->where('production_id', '=', $this->production_id)
                ->where('event_type', '=', WorkCenter::DOWNTIME_STATUS_UNPLAN_DIE_CHANGE)
                ->first();

            if (!$unplanned) {
                //no unplanned event, generate new
                $unplanned = new DowntimeEvent();
                $unplanned->production_id = $this->production_id;
                $unplanned->event_type = WorkCenter::DOWNTIME_STATUS_UNPLAN_DIE_CHANGE;
                $unplanned->broadcast_status = 1;
                $unplanned->setConnection($this->connection);
            }

            $unplanned->start_time = $this->end_time;

            if ($production->die_change_end_at != null) {
                //unplanned also ended
                $unplanned->endEvent($production->die_change_end_at)->save();
                $unplanned->broadcast_status = 2;
            }

            $unplanned->save();
        }

        return $this;
    }

    public function dispatchBroadcastStartEvent()
    {
        if (!$this->start_time) //delay dispatch only with start time
            return $this;

        /** @var \App\Models\Production $production */
        $production = $this->production;
        if (!$production)
            return $this;

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $production->workCenter;
        if (!$workCenter)
            return $this;

        /** @var \App\Models\Plant $plant */
        $plant = $workCenter->plant;
        if (!$plant)
            return $this;

        DowntimeEventStarted::dispatch($plant, $this)->delay($this->start_time);
        return $this;
    }
    public function broadcastStartEvent()
    {
        if (
            $this->broadcast_status >= 1 || //event already broadcasted
            !$this->start_time || //invalid start time
            $this->start_time > new \DateTime() //event not started yet
        )
            return $this;

        //Mark end time event as broadcasted
        $this->broadcast_status = 1;
        $this->save();

        /** @var \App\Models\Production $production */
        $production = $this->production;
        if (!$production)
            return $this;

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $production->workCenter;
        if (!$workCenter)
            return $this;

        //update work center downtime state & force broadcast WorkCenterDataUpdated
        $production->updateHourlySummary()->save();
        $workCenter->updateDowntimeState(true);

        if ($this->event_type == WorkCenter::DOWNTIME_STATUS_PLAN_BREAK)
            $workCenter->checkBreakSignal();

        return $this;
    }

    public function dispatchBroadcastEndEvent()
    {
        if (!$this->end_time) //delay dispatch only with end time
            return $this;

        /** @var \App\Models\Production $production */
        $production = $this->production;
        if (!$production)
            return $this;

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $production->workCenter;
        if (!$workCenter)
            return $this;

        /** @var \App\Models\Plant $plant */
        $plant = $workCenter->plant;
        if (!$plant)
            return $this;

        DowntimeEventEnded::dispatch($plant, $this)->delay($this->end_time);
        return $this;
    }
    public function broadcastEndEvent()
    {
        if (
            $this->broadcast_status >= 2 || //event already broadcasted
            !$this->end_time || //invalid end time
            $this->end_time > new \DateTime() //event not ended yet
        )
            return $this;

        //Mark end time event as broadcasted
        $this->broadcast_status = 2;
        $this->save();

        /** @var \App\Models\Production $production */
        $production = $this->production;
        if (!$production)
            return $this;

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $production->workCenter;
        if (!$workCenter)
            return $this;

        //update work center downtime state & force broadcast WorkCenterDataUpdated
        $production->updateHourlySummary()->save();
        $workCenter->updateDowntimeState(true);

        if ($this->event_type == WorkCenter::DOWNTIME_STATUS_PLAN_BREAK)
            $workCenter->checkBreakSignal();

        return $this;
    }

    public function setDowntimeReason($reason, $userInputReason = null, $userId = null)
    {
        if (!$userId)
            $user = User::getCurrent();
        else
            $user = User::find($userId);

        $this->reason = $reason;
        $this->user_input_reason = $userInputReason;
        $this->user_id = $user ? $user->id : null;

        return $this;
    }

    //relationships

    //belongto production_id
    public function production()
    {
        return $this->belongsTo(Production::class, 'production_id', 'id');
    }

    //belongto downtime_id
    public function downtime()
    {
        return $this->belongsTo(Downtime::class, 'downtime_id', 'id');
    }

    //belongto reason_reason
    public function reason()
    {
        return $this->belongsTo(Reason::class, 'reason', 'reason');
    }

    //belongto user_id
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
