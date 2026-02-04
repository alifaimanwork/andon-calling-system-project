<?php

namespace App\Models;

use App\Events\Terminal\RejectSettingsUpdatedEvent;
use App\Events\Terminal\WorkCenterDowntimeStateChangeEvent;
use App\Extras\Casts\AsNullableArrayObject;
use App\Extras\Payloads\DowntimeData;
use App\Extras\Payloads\DowntimeReasonData;
use App\Extras\Payloads\PendingData;
use App\Extras\Payloads\RejectData;
use App\Extras\Payloads\GenericRequestResult;
use App\Extras\Payloads\RejectSettingData;
use App\Extras\Support\RejectSettingUpdateInfo;
use App\Extras\Utils\ExcelTemplate;
use App\Jobs\HourlyProductionUpdate;
use App\Jobs\LastCycleStopSignal;
use App\Jobs\PlanDieChangeExpired;
use App\Jobs\ProcessProductionClosing;
use App\Jobs\ShiftEnded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned integer
 * 
 * @property int $work_center_id Foreign Key (workCenter): unsigned integer
 * @property int $user_id Foreign Key (user): unsigned integer
 * @property int $shift_type_id Foreign Key (shiftType): unsigned integer
 * 
 * @property int $setup_time integer
 * @property array $die_change_info string
 * @property int $status tinyinteger - TODO: define production status code
 * @property array $hourly_summary hourly Summary
 * @property array $runtime_summary mediumText
 * 
 * @property float $average_oee average_oee
 * @property float $average_availability average_availability
 * @property float $average_performance average_performance
 * @property float $average_quality average_quality
 * 
 * 
 * @property array $schedule_data schedule snapshot data
 * 
 * @property \Carbon\Carbon $started_at timestamp
 * @property \Carbon\Carbon $stopped_at timestamp
 * @property \Carbon\Carbon $die_change_end_at timestamp
 * @property \Carbon\Carbon $created_at timestamp
 * @property \Carbon\Carbon $updated_at timestamp
 * @property int $last_hourly_production_update
 * */
class Production extends Model
{

    /** Production Stopped */
    const STATUS_STOPPED = 0;
    /** Production Die Change */
    const STATUS_DIE_CHANGE = 1;
    /** Production Running */
    const STATUS_RUNNING = 3;
    /** Production Canceled */
    const STATUS_CANCELED = 8;

    const TABLE_NAME = 'productions';
    protected $table = self::TABLE_NAME;


    protected $cachedRuntimeSummary = null;

    protected $guarded = [];

    protected $appends = [
        'runtime_summary',
    ];
    protected $casts = [
        'started_at' => 'datetime',
        'stopped_at' => 'datetime',
        'die_change_end_at' => 'datetime',
        'schedule_data' => AsArrayObject::class,
        'die_change_info' => AsNullableArrayObject::class,
        'hourly_summary' => AsNullableArrayObject::class,
        'runtime_summary_cache' => AsNullableArrayObject::class
    ];



    //-- Accessor --//
    public function getRuntimeSummaryAttribute()
    {
        // if (!$this->runtime_summary_cache)
        //     $this->updateRuntimeSummaryCache()->save();

        // return $this->runtime_summary_cache;

        if (($this->status == self::STATUS_STOPPED || $this->status == self::STATUS_CANCELED) && (is_array($this->runtime_summary_cache) && count($this->runtime_summary_cache))) {
            return $this->runtime_summary_cache;
        }
        if ($this->cachedRuntimeSummary)
            return $this->cachedRuntimeSummary;

        $downtimeEvents = $this->downtimeEvents()->where('start_time', '<=', date('Y-m-d H:i:s'))->get();
        $this->cachedRuntimeSummary = $this->generateRuntimeSummary($downtimeEvents);
        return $this->cachedRuntimeSummary;
    }


    private function generateRuntimeSummary($downtimeEvents, $startTime = null, $endTime = null)
    {
        $now = new \DateTime();

        if (!$startTime)
            $startTime = $this->started_at;

        if (!$endTime || $endTime > $this->stopped_at)
            $endTime = $this->stopped_at;

        $allDowntimes = [];

        $unplanDowntimes = [];
        $unplanHumanDieChangeDowntimes = [];
        $planDowntimes = [];

        $planBreakTimes = [];

        $humanDowntimes = [];
        $machineDowntimes = [];

        $dieChange = [];
        $unplanDieChange = [];
        $planDieChange = [];

        $byDowntimeIds = [];


        /** @var \App\Models\DowntimeEvent $downtimeEvent */
        foreach ($downtimeEvents as &$downtimeEvent) {
            $allDowntimes[] = $downtimeEvent;

            if ($downtimeEvent->downtime_id) {
                if (!isset($byDowntimeIds[$downtimeEvent->downtime_id]))
                    $byDowntimeIds[$downtimeEvent->downtime_id] = [];

                $byDowntimeIds[$downtimeEvent->downtime_id][] = $downtimeEvent;
            }

            if ($downtimeEvent->event_type == WorkCenter::DOWNTIME_STATUS_UNPLAN_DIE_CHANGE) {
                $unplanDieChange[] = $downtimeEvent;
                $dieChange[] = $downtimeEvent;
                $unplanHumanDieChangeDowntimes[] = $downtimeEvent;
            }

            if ($downtimeEvent->event_type == WorkCenter::DOWNTIME_STATUS_PLAN_DIE_CHANGE) {
                $planDieChange[] = $downtimeEvent;
                $dieChange[] = $downtimeEvent;
            }

            if ($downtimeEvent->event_type == WorkCenter::DOWNTIME_STATUS_PLAN_BREAK)
                $planBreakTimes[] = $downtimeEvent;


            if ($downtimeEvent->event_type > 0) //Plan downtime > 0
            {
                $planDowntimes[] = $downtimeEvent;
            } elseif ($downtimeEvent->event_type < 0) //Unplan downtime < 0
            {
                $unplanDowntimes[] = $downtimeEvent;

                if ($downtimeEvent->event_type == WorkCenter::DOWNTIME_STATUS_UNPLAN_HUMAN) {
                    $humanDowntimes[] = $downtimeEvent;
                    $unplanHumanDieChangeDowntimes[] = $downtimeEvent;
                } elseif ($downtimeEvent->event_type == WorkCenter::DOWNTIME_STATUS_UNPLAN_MACHINE)
                    $machineDowntimes[] = $downtimeEvent;
            }

            $downtimeEvent->start_timestamp_cache = $downtimeEvent->start_time->getTimestamp();
            if ($downtimeEvent->end_time)
                $downtimeEvent->end_timestamp_cache = $downtimeEvent->end_time->getTimestamp();

            unset($downtimeEvent);
        }
        $summary = new \stdClass();


        $allRuntime = $this->summarizeRuntime([], $startTime, $endTime);

        $goodRuntime = $this->summarizeRuntime($allDowntimes, $startTime, $endTime);
        $planRuntime = $this->summarizeRuntime($planDowntimes, $startTime, $endTime);

        $nonBreakRuntime = $this->summarizeRuntime($planBreakTimes, $startTime, $endTime);

        $summary->runtimes = [
            'good' => $goodRuntime,
            'plan' => $planRuntime
        ];

        $planRuntimeActivePeriod = $planRuntime['active_period'];
        $blockActivePeriod = $allRuntime['active_period'];

        $downtimeByIds = [];
        foreach ($byDowntimeIds as $id => $events) {
            $downtimeByIds[$id] = DowntimeEvent::summarize($events, $planRuntimeActivePeriod);
        }
        $summary->downtimes = [
            'by_id' => $downtimeByIds,
            'all' => DowntimeEvent::summarize($allDowntimes, $blockActivePeriod),
            'unplan' => DowntimeEvent::summarize($unplanDowntimes, $planRuntimeActivePeriod),
            'plan' => DowntimeEvent::summarize($planDowntimes, $blockActivePeriod),
            'plan_break' => DowntimeEvent::summarize($planBreakTimes, $blockActivePeriod),
            'unplan_human_die_change' => DowntimeEvent::summarize($unplanHumanDieChangeDowntimes, $planRuntimeActivePeriod),
            'unplan_human' => DowntimeEvent::summarize($humanDowntimes, $planRuntimeActivePeriod),
            'unplan_machine' => DowntimeEvent::summarize($machineDowntimes, $planRuntimeActivePeriod),
            'die_change' => DowntimeEvent::summarize($dieChange, $nonBreakRuntime['active_period']),
            'plan_die_change' => DowntimeEvent::summarize($planDieChange, $nonBreakRuntime['active_period']),
            'unplan_die_change' => DowntimeEvent::summarize($unplanDieChange, $planRuntimeActivePeriod),
        ];

        $summary->summary = $this->summarizeProductionRun($allDowntimes);
        return (array)$summary;
    }

    public function summarizeProductionRun($downtimes)
    {
        $startMicroTime = microtime(true);
        $now = new \DateTime();
        $nowTimestamp = $now->getTimestamp();

        $eventTimes = [];
        $eventTimes[$this->started_at->getTimestamp()] = ['time' => $this->started_at];
        if ($this->stopped_at && $now >= $this->stopped_at)
            $eventTimes[$this->stopped_at->getTimestamp()] = ['time' => $this->stopped_at];


        /** @var \App\Models\DowntimeEvent $downtime */
        foreach ($downtimes as $downtime) {
            if ($downtime->end_timestamp_cache &&  $downtime->start_timestamp_cache >= $downtime->end_timestamp_cache)
                continue;

            if ($nowTimestamp < $downtime->start_timestamp_cache)
                continue;

            //$downtime->start_timestamp = $downtime->start_time->getTimestamp();
            $eventTimes[$downtime->start_timestamp_cache] = ['time' => $downtime->start_timestamp_cache];

            //$downtime->end_timestamp = $downtime->end_time->getTimestamp();
            if ($downtime->end_timestamp_cache && $nowTimestamp >= $downtime->end_timestamp_cache)
                $eventTimes[$downtime->end_timestamp_cache] = ['time' => $downtime->end_timestamp_cache];

            // unset($downtime);
        }

        ksort($eventTimes);

        $map = DowntimeEvent::getPriorityMap();

        foreach ($eventTimes as $timestamp => &$eventTime) {
            //get state at given time

            //activeDowtimes during event
            $currentState = WorkCenter::DOWNTIME_STATUS_NONE;

            foreach ($downtimes as $downtime) {

                if (isset($map[$downtime->event_type]) && isValueInsideRange($timestamp, $downtime->start_timestamp_cache, $downtime->end_timestamp_cache)) { //if (isset($map[$downtime->event_type]) && isValueInsideRange($eventTime['time'], $downtime->start_time, $downtime->end_time)) {

                    if ($map[$currentState] > $map[$downtime->event_type]) //downtime has higher priority, override state
                        $currentState = $downtime->event_type;
                }
            }

            $eventTime['state'] = $currentState;

            unset($eventTime);
        }

        $result = [];
        foreach ($eventTimes as $key => $eventTime) {
            $result[] = $eventTime;
        }

        return [
            'ongoing' => (!$this->stopped_at || $this->stopped_at > new \DateTime()),
            'data' => $result
        ];
    }

    public function updateRuntimeSummaryCache()
    {
        //cache runtime_summary at the end of production
        $downtimeEvents = $this->downtimeEvents()->where('start_time', '<=', date('Y-m-d H:i:s'))->get();
        $this->runtime_summary_cache = $this->generateRuntimeSummary($downtimeEvents);
        return $this;
    }

    public function getTotalTimerDuration($timeType, $timerTag)
    {
        if (!isset($this->runtime_summary[$timeType]))
            return 0;

        $timerGroup = $this->runtime_summary[$timeType];

        if (!isset($timerGroup[$timerTag], $timerGroup[$timerTag]['duration']) || !is_numeric($timerGroup[$timerTag]['duration']))
            return 0;

        $duration = $timerGroup[$timerTag]['duration'];


        if (isset($timerGroup[$timerTag]['ongoing']) && is_numeric($timerGroup[$timerTag]['ongoing'])) {
            $duration += (new \DateTime())->getTimestamp() - $timerGroup[$timerTag]['ongoing'];
        }
        return $duration;
    }

    public function cancelProduction()
    {
        $this->stopped_at = new \DateTime();
        $this->die_change_end_at = $this->stopped_at;

        $this->status = self::STATUS_CANCELED;
        $this->updateHourlySummary()->save();

        foreach ($this->productionLines as $productionLine) {
            /** @var \App\Models\ProductionOrder $productionOrder */
            $productionOrder = $productionLine->productionOrder ?? null;
            if (!$productionOrder)
                continue;

            // change production order status from STATUS_ONGOING to STATUS_INCOMPLETE / STATUS_PPS (if no production ever run)
            // TODO: need confirm with ingress status change flow
            if ($productionOrder->status == ProductionOrder::STATUS_ONGOING) {
                if ($productionOrder->productions()->where(Production::TABLE_NAME . '.status', '!=', Production::STATUS_CANCELED)->first())
                    $productionOrder->status = ProductionOrder::STATUS_INCOMPLETE;
                else
                    $productionOrder->status = ProductionOrder::STATUS_PPS;

                $productionOrder->save();
            }
        }

        return $this;
    }

    public function stopProduction()
    {
        $startMicroTime = microtime(true);
        $now = new \DateTime();
        $this->status = self::STATUS_STOPPED;
        if (!$this->stopped_at || $this->stopped_at > $now)
            $this->stopped_at = $now;

        $this->save();
        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $this->workCenter;

        if (!$workCenter)
            return $this;

        /** @var \App\Models\Plant $plant */
        $plant = $workCenter->plant;

        if (!$plant)
            return $this;

        //update production order status
        foreach ($this->productionLines as $productionLine) {

            /** @var \App\Models\ProductionOrder $productionOrder */
            $productionOrder = $productionLine->productionOrder ?? null;
            if (!$productionOrder)
                continue;

            if ($productionOrder->getBalancePlanQuantity() <= 0)
                $productionOrder->status = ProductionOrder::STATUS_COMPLETED; //set status incomplete ATM. should turn completed if meet target
            else
                $productionOrder->status = ProductionOrder::STATUS_INCOMPLETE;

            $productionOrder->save();
        }

        //dispatch end of production summarize job
        dispatch(new ProcessProductionClosing($plant, $this))->onQueue('low');
        return $this;
    }
    public function endOfProductionProcess()
    {


        $this->updateHourlySummary(false);
        $this->updateRuntimeSummaryCache();
        $this->save();
        /** @var \App\Models\ProductionLine $productionLine */
        foreach ($this->productionLines as $productionLine) {
            //update everything
            $productionLine->recalculateTagCount()->updateRejectCount()->updatePendingCount()->updateHourlySummary($this->hourly_summary)->updateOverallSummary($this);


            //double confirm ok part
            /** @var \App\Models\ProductionOrder $productionOrder */
            $productionOrder = $productionLine->productionOrder;

            if ($productionOrder->getBalancePlanQuantity() <= 0)
                $productionOrder->status = ProductionOrder::STATUS_COMPLETED; //set status incomplete ATM. should turn completed if meet target
            else
                $productionOrder->status = ProductionOrder::STATUS_INCOMPLETE;

            $productionOrder->save();


            $productionLine->rework_status = ($productionLine->pending_count > 0) ? ProductionLine::REWORK_STATUS_OPEN : ProductionLine::REWORK_STATUS_COMPLETED; //no pending, close rework
            $productionLine->save();


            //SAP Exports

            //GRNG
            if ($productionLine->reject_count > 0) {
                $productionLine->exportGRNG($this->user);
            }

            //GRQI
            if ($productionLine->pending_count > 0) {
                $productionLine->exportGRQI($this->user);
            }

            $productionLine
                ->exportETTOP10()
                ->exportETTOP20()
                ->exportPendingGROK($this->user, true); //export balance ok count
        }
        $this->updateAverageOee()->save();
        return $this;
    }
    public function updateAverageOee()
    {
        $this->average_oee = 0;
        $this->average_availability = 0;
        $this->average_performance = 0;
        $this->average_quality = 0;

        /** @var \App\Models\ProductionLine $productionLine */
        foreach ($this->productionLines as $productionLine) {
            $this->average_oee += $productionLine->oee;
            $this->average_availability += $productionLine->availability;
            $this->average_performance += $productionLine->performance;
            $this->average_quality += $productionLine->quality;
        }

        if (count($this->productionLines) > 0) {
            $this->average_oee /= count($this->productionLines);
            $this->average_availability /= count($this->productionLines);
            $this->average_performance /= count($this->productionLines);
            $this->average_quality /= count($this->productionLines);
        }

        return $this;
    }

    public function startProduction()
    {
        if ($this->status == self::STATUS_DIE_CHANGE) {
            $this->die_change_end_at = new \DateTime();

            //end diechange downtime event
            $dieChangeDowntimeEvents = $this->downtimeEvents()
                ->where(
                    function (Builder $q) {
                        $q->where('event_type', '=', WorkCenter::DOWNTIME_STATUS_PLAN_DIE_CHANGE)
                            ->orWhere('event_type', '=', WorkCenter::DOWNTIME_STATUS_UNPLAN_DIE_CHANGE);
                    }
                )
                ->get();

            /** @var \App\Models\DowntimeEvent $downtimeEvent */
            foreach ($dieChangeDowntimeEvents as $downtimeEvent) {

                if (!$downtimeEvent->end_time || $downtimeEvent->end_time > $this->die_change_end_at) {
                    $downtimeEvent->endEvent($this->die_change_end_at)->save();
                    $downtimeEvent->broadcast_status = 2; //mark broadcasted. will be broadcasted at workcenter startProduction()
                }
            }
        }

        /** @var \App\Models\ProductionLine $productionLine */
        foreach ($this->productionLines as $productionLine) {

            /** @var \App\Models\ProductionOrder $productionOrder */
            $productionOrder = $productionLine->productionOrder ?? null;
            if (!$productionOrder)
                continue;



            // change production order status to STATUS_ONGOING (should already ongoing since status from die change process)
            if ($productionOrder->status != ProductionOrder::STATUS_ONGOING) {
                $productionOrder->status = ProductionOrder::STATUS_ONGOING;
                $productionOrder->save();
            }
        }

        $this->updateHourlySummary()->save();
        return $this;
    }

    public function startDieChange()
    {
        foreach ($this->productionLines as $productionLine) {
            $productionOrder = $productionLine->productionOrder ?? null;
            if (!$productionOrder)
                continue;

            $productionOrder->status = ProductionOrder::STATUS_ONGOING;
            $productionOrder->save();
        }



        $this->status = self::STATUS_DIE_CHANGE;


        //generate all plan break time
        $breakTimeSlots = $this->schedule_data['breaks'];
        foreach ($breakTimeSlots as &$breakTime) {
            $startTime = new \DateTime($breakTime['start_time']);
            $endTime = new \DateTime($breakTime['end_time']);
            $c[] = [$startTime->format('c'), $endTime->format('c')];
            if (!isValueRangeOverlap($this->started_at, $this->stopped_at, $startTime, $endTime)) {
                unset($breakTime);
                continue;
            }


            //Trim breaktime to production start & end of shift
            if ($startTime < $this->started_at)
                $startTime = $this->started_at;
            if ($endTime > $this->stopped_at)
                $endTime = $this->stopped_at;



            $breakTimeEvent = new DowntimeEvent();
            $breakTimeEvent->production_id = $this->id;
            $breakTimeEvent->event_type = WorkCenter::DOWNTIME_STATUS_PLAN_BREAK;

            $breakTimeEvent->start_time = $startTime;
            $breakTimeEvent->end_time = $endTime;
            $breakTimeEvent->setConnection($this->connection)->save();

            if ($breakTimeEvent->start_time > $this->started_at)
                $breakTimeEvent->dispatchBroadcastStartEvent(); //trigger broadcast on start event

            if ($breakTimeEvent->end_time > $this->started_at)
                $breakTimeEvent->dispatchBroadcastEndEvent(); //trigger broadcast on end event

            unset($breakTime);
        }

        //generate die-change downtime event
        $dieChangeDowntimeEvent = new DowntimeEvent();
        $dieChangeDowntimeEvent->production_id = $this->id;
        $dieChangeDowntimeEvent->event_type = WorkCenter::DOWNTIME_STATUS_PLAN_DIE_CHANGE;
        $dieChangeDowntimeEvent->start_time = $this->started_at;
        $dieChangeDowntimeEvent->end_time = $this->getDieChangeExpireTime(); //set end time
        $dieChangeDowntimeEvent->setConnection($this->connection)->save();


        //regenerate all ongoing machine downtime
        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $this->workCenter;
        if ($workCenter) {

            $pendingDowntimes = [];
            //force resync with tag
            $workCenterDowntimes = $workCenter->workCenterDowntimes()->whereNotNull('opc_tag_id')->with('opcTag')->get();
            foreach ($workCenterDowntimes as $workCenterDowntime) {
                if (!$workCenterDowntime->opcTag)
                    continue;

                $workCenterDowntime->opcTag->syncFromActiveTag();
                $workCenterDowntime->state = $workCenterDowntime->opcTag->value ? 1 : 0;
                $workCenterDowntime->save();

                if ($workCenterDowntime->state) {
                    $pendingDowntimes[] = $workCenterDowntime;
                }
            }


            //$pendingDowntimes = $workCenter->workCenterDowntimes()->where('state', '=', 1)->get();

            /** @var \App\Models\WorkCenterDowntime $pendingDowntime */
            foreach ($pendingDowntimes as $pendingDowntime) {
                //retrigger
                /** @var \App\Models\DowntimeEvent $downtimeEvent */
                $downtimeEvent = new DowntimeEvent();

                /** @var \App\Models\Downtime $downtime */
                $downtime = $pendingDowntime->downtime;

                if ($downtime->downtime_type_id == 1) //hardcoded downtime type
                    $downtimeEvent->event_type =  $pendingDowntime->WorkCenter::DOWNTIME_STATUS_UNPLAN_MACHINE;
                else
                    $downtimeEvent->event_type =  WorkCenter::DOWNTIME_STATUS_UNPLAN_HUMAN;

                $downtimeEvent->production_id = $this->id;
                $downtimeEvent->downtime_id = $downtime->id;
                $downtimeEvent->start_time = $this->started_at;

                $downtimeEvent->setConnection($this->connection);
                $downtimeEvent->save();
            }
        }

        //die change expire job
        $dieChangeDowntimeEvent->dispatchPlanDieChangeExpireEvent();

        $this->updateHourlySummary()->save();

        //schedule update hourly job
        $nextScheduleTime = HourlyProductionUpdate::getNextScheduleTime($workCenter->plant, $this);
        if ($nextScheduleTime)
            dispatch(new HourlyProductionUpdate($workCenter->plant, $this))->delay($nextScheduleTime);

        //early trigger off production on last cycle
        $maxCycleTime = $this->getMaxCycleTime();
        if ($maxCycleTime > 0) {
            $earlyTrigger = clone $this->stopped_at->toDateTime();
            $earlyTrigger->sub(new \DateInterval('PT' . $maxCycleTime . 'S'));
            dispatch(new LastCycleStopSignal($workCenter, $this))->delay($earlyTrigger); //send OnProd = 0 on last cycle
        }

        //shift ended job
        dispatch(new ShiftEnded($workCenter->plant, $this))->delay($this->stopped_at);

        return $this;
    }

    public function getMaxCycleTime()
    {
        $maxCycleTime = 0;
        /** @var \App\Models\ProductionLine $productionLine */
        foreach ($this->productionLines as $productionLine) {
            $cycleTime = $productionLine->part_data['cycle_time'] ?? 0;
            if ($maxCycleTime < $cycleTime)
                $maxCycleTime = $cycleTime;
        }
        return $maxCycleTime;
    }

    public function shiftEnded()
    {
        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $this->workCenter;
        if (!$workCenter || $workCenter->current_production_id != $this->id)
            return;



        $workCenter->setStopProduction(true);
    }

    public function getDieChangeExpireTime(): \DateTime
    {
        $startTime = $this->started_at->toDateTime();
        $endTime = clone $startTime;
        if (!$this->setup_time || $this->setup_time <= 0)
            return $endTime;

        $endTime->add(new \DateInterval('PT' . $this->setup_time . 'S'));

        //Extend die change time if overlap with breaktime (Assume breaktime not overlap)
        $breakTimeEvents = $this->downtimeEvents()->where('event_type', '=', WorkCenter::DOWNTIME_STATUS_PLAN_BREAK)->orderBy('start_time')->get();

        /** @var \App\Models\DowntimeEvent $breakTime */
        foreach ($breakTimeEvents as $breakTime) {

            if (!isValueRangeOverlap($startTime, $endTime, $breakTime->start_time, $breakTime->end_time))
                continue;
            $breakDuration = ($breakTime['end_time']->getTimestamp() - $breakTime['start_time']->getTimestamp());
            $endTime->add(new \DateInterval('PT' . $breakDuration . 'S'));
        }

        return $endTime;
    }

    //-------- First Production Confirmation --------
    public function setRejectSettings(RejectSettingData $rejectSettingData): GenericRequestResult
    {
        $user =  $user ?? User::getCurrent() ?? null;
        if (!$user)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid parameters"); //critical error, user should not be null


        /** @var \App\Models\ProductionLine $productionLine */
        $productionLine = $this->productionLines()->where('id', $rejectSettingData->production_line_id)->first();

        if (!$productionLine)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid parameters");



        $rejectsData = [];

        $maintenanceCount = intval($rejectSettingData->maintenance_count);
        $qualityCount = intval($rejectSettingData->quality_count);

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $this->workCenter;

        //find maintenance reject type TODO: add special identifier for special reject type
        /** @var \App\Models\Plant $plant */
        $plant = $this->workCenter->plant;


        if ($maintenanceCount > 0) {
            $rejectType = $plant->onPlantDb()->rejectTypes()->where('tag', '=', 'maintenance')->first();
            if ($rejectType)
                $rejectsData[] = new RejectData($productionLine->id, $rejectType->id, $maintenanceCount);
        }
        if ($qualityCount > 0) {
            $rejectType = $plant->onPlantDb()->rejectTypes()->where('tag', '=', 'quality')->first();
            if ($rejectType)
                $rejectsData[] = new RejectData($productionLine->id, $rejectType->id, $qualityCount);
        }

        $productionLine->setReject($rejectsData, $workCenter, $this);

        return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK", [
            'production_line_id' => $productionLine->id,
            'line_no' => $productionLine->line_no
        ]);
    }


    //-------- Set Human Downtime ------------
    public function setDowntime(DowntimeData $downtimeData, $downtimeTypeId = DowntimeType::HUMAN_DOWNTIME): GenericRequestResult
    {
        //$workCenterDowntimeId, $downtimeId, $state, $downtimeTypeId = DowntimeType::HUMAN_DOWNTIME

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $this->workCenter;

        /** @var \App\Models\WorkCenterDowntime $workCenterDowntime */
        $workCenterDowntime = $workCenter->workCenterDowntimes()->where(WorkCenterDowntime::TABLE_NAME . '.id', '=', $downtimeData->work_center_downtime_id)->first();

        /** @var \App\Models\Downtime $downtime */
        $downtime = $workCenter->downtimes()->where(Downtime::TABLE_NAME . '.id', '=', $downtimeData->downtime_id)->where('downtime_type_id', '=', $downtimeTypeId)->first();

        if (
            !$workCenter || !$workCenterDowntime || !$downtime ||
            $workCenterDowntime->downtime_id != $downtime->id || //Downtime input not matched
            $workCenter->id != $workCenterDowntime->work_center_id //WorkCenter not matched
        ) {
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid downtime");
        }

        $oldState = $workCenterDowntime->state ? 1 : 0;
        $newState = $downtimeData->set_state ? 1 : 0;



        if ($oldState == $newState) {

            if (!$newState) {
                //check unclosed downtime
                $downtimeEvent = $this->downtimeEvents()->where('downtime_id', $downtime->id)->whereNull('end_time')->first();
                if ($downtimeEvent)
                    $downtimeEvent->endEvent()->save();
            }

            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_STATUS, "State already set");
        }

        $workCenterDowntime->state = $newState;
        $workCenterDowntime->save();

        // /** @var \App\Models\DowntimeEvent $downtimeEvent */
        // $downtimeEvent = $this->downtimeEvents()->where('downtime_id', $downtime->id)->whereNull('end_time')->first();

        // $eventType = $downtimeTypeId == DowntimeType::HUMAN_DOWNTIME ? WorkCenter::DOWNTIME_STATUS_UNPLAN_HUMAN : WorkCenter::DOWNTIME_STATUS_UNPLAN_MACHINE;

        // if ($newState && !$downtimeEvent) {
        //     //trigger downtime event start

        //     /** @var \App\Models\DowntimeEvent $downtimeEvent */
        //     $downtimeEvent = new DowntimeEvent();
        //     $downtimeEvent->production_id = $this->id;
        //     $downtimeEvent->downtime_id = $downtime->id;
        //     $downtimeEvent->event_type = $eventType;
        //     $downtimeEvent->setConnection($this->connection);
        //     $downtimeEvent->start_time = date('Y-m-d H:i:s');
        //     $downtimeEvent->save();
        // } elseif (!$newState && $downtimeEvent) {
        //     //trigger downtime event end;
        //     $downtimeEvent->endEvent()->save();
        // }

        $this->triggerDowntime($newState, date('Y-m-d H:i:s'), $workCenterDowntime, $downtime);

        //$this->updateHourlySummary()->save();
        dispatch(new HourlyProductionUpdate($workCenter->plant, $this, false));

        //broadcast WorkCenterData updated
        $workCenter->updateDowntimeState(true)->save();

        $workCenter->checkHumanDowntimeTag();

        try {
            event(new WorkCenterDowntimeStateChangeEvent($workCenter));
        } catch (\Exception $ex) {
        }


        return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK");
    }

    //DB Utils
    public function triggerDowntime($downtimeState, $valueUpdatedAt, WorkCenterDowntime $workCenterDowntime, Downtime $downtime)
    {
        $workCenter = $this->workCenter;

        DB::connection($this->connection)
            ->transaction(function () use ($downtimeState, $valueUpdatedAt, $workCenter, $workCenterDowntime, $downtime) {

                $opcTag = DB::connection($this->connection)->table(OpcTag::TABLE_NAME)->lockForUpdate()->find($workCenterDowntime->opc_tag_id);

                if ($opcTag) {
                    DB::connection($this->connection)->update(
                        'UPDATE `' . OpcTag::TABLE_NAME . '` ' .
                            'SET `prev_value`= ?, `value`= ?, `value_updated_at` = ? ' .
                            'WHERE `id` = ?',
                        [$opcTag->value, $downtimeState, $valueUpdatedAt, $workCenterDowntime->id]
                    ); //opc tag
                }

                $workCenterDowntimeRecord = DB::connection($this->connection)->table(WorkCenterDowntime::TABLE_NAME)->lockForUpdate()->find($workCenterDowntime->id);

                DB::connection($this->connection)->update(
                    'UPDATE `' . WorkCenterDowntime::TABLE_NAME . '` ' .
                        'SET `state`= ?, `value_updated_at` = ? ' .
                        'WHERE `id` = ?',
                    [$downtimeState, $valueUpdatedAt, $workCenterDowntime->id]
                ); //work center downtime

                if ($downtimeState) {
                    DB::connection($this->connection)->insert(
                        'INSERT INTO `' . DowntimeEvent::TABLE_NAME . '` ' .
                            '(`event_type`,`production_id`,`downtime_id`,`start_time`) ' .
                            'SELECT ?,?,?,? ' .
                            'WHERE NOT EXISTS ' .
                            '(SELECT `id` FROM `' . DowntimeEvent::TABLE_NAME . '` ' .
                            'WHERE `production_id` = ? AND ' .
                            '`downtime_id` = ? AND ' .
                            '`end_time` IS NULL AND ' .
                            '`start_time` <= ?)',
                        [
                            $downtime->downtime_type_id == 1 ? WorkCenter::DOWNTIME_STATUS_UNPLAN_MACHINE : WorkCenter::DOWNTIME_STATUS_UNPLAN_HUMAN, $workCenter->current_production_id, $downtime->id, $valueUpdatedAt,
                            $workCenter->current_production_id, $downtime->id, $valueUpdatedAt
                        ]
                    ); //create event
                } else {
                    DB::connection($this->connection)->update(
                        'UPDATE `' . DowntimeEvent::TABLE_NAME . '` ' .
                            'SET `end_time`= ? ' .
                            'WHERE `production_id` = ? AND downtime_id = ? AND start_time <= ? AND end_time IS NULL',
                        [$valueUpdatedAt, $workCenter->current_production_id, $workCenterDowntime->downtime_id, $valueUpdatedAt]
                    ); //Close event
                }

                return true;
            }, 10);
    }

    //Production Utils

    public function updateHourlySummary($updateProductionLines = true)
    {
        $this->hourly_summary = [];

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $this->workCenter;

        if (!$workCenter)
            return $this;

        /** @var \App\Models\Plant $plant */
        $plant = $workCenter->plant;

        if (!$plant)
            return $this;

        $localTimeZone = $plant->getLocalDateTimeZone();
        $utcTimeZone = new \DateTimeZone('UTC');
        //pre allocate blocks
        $current = clone $this->started_at;
        $now = new \DateTime();

        $end = $this->stopped_at ?? $now;
        if ($end > $now)
            $end = $now;

        //quick check duration
        $duration = $end->getTimestamp() - $current->getTimestamp();
        if ($duration < 0 || $duration > 86400) //Invalid start & end
            return $this;


        $hourInterval = new \DateInterval('PT1H');

        $downtimeEvents = $this->downtimeEvents()->where('start_time', '<=', $end->format('Y-m-d H:i:s'))->get();


        $blocks = [];
        $this->hourly_summary = [];
        $blockCount = 0;
        while (($current < $end || $blockCount == 0) && $blockCount < 24) {
            $blockCount++;
            $currentLocal = clone $current;
            $currentLocal->setTimezone($localTimeZone);

            $endBlock = \DateTime::createFromFormat('Y-m-d H:i:s', $currentLocal->format('Y-m-d H:') . '00:00', $localTimeZone); //Use localtime for hourly block
            $endBlock->setTimezone($utcTimeZone);
            $endBlock->add($hourInterval);
            if ($endBlock > $end)
                $endBlock = clone $end;

            $blockLabel = $currentLocal->format('H');

            $localStart = clone $current;
            $localStart->setTimezone($localTimeZone);

            $localEnd = clone $endBlock;
            $localEnd->setTimezone($localTimeZone);
            //find block
            /*
                actual_output: 0
                availability: 1
                oee: 0
                ok_count: -4
                pending_count: 0
                performance: 0
                plan_quantity: 960
                plan_variance: 964
                quality: 1
                reject_count: 4
                standard_output: 6
                variance: 10
            */

            if (!isset($this->hourly_summary[$blockLabel])) {
                $this->hourly_summary[$blockLabel] = [

                    //Time
                    'start' => $current->format('c'),
                    'end' => $endBlock->format('c'),
                    'local_start' => $localStart->format('c'),
                    'local_end' => $localEnd->format('c'),

                    //Duration
                    'runtime_summary' => (array)$this->generateRuntimeSummary($downtimeEvents, $current, ($endBlock == $now) ? null : $endBlock),
                    'runtime_summary_accumulate' => ['runtimes' =>
                    [], 'downtimes' => [
                        'by_id' => [],
                    ]],
                ];
                $blocks[] = &$this->hourly_summary[$blockLabel];
            }
            $current = $endBlock;
        }
        usort($blocks, function ($a, $b) {
            return $a['start'] <=> $b['start'];
        });
        $runtimeSummaryAccumulate = ['runtimes' =>
        [], 'downtimes' => [
            'by_id' => [],
        ]];
        foreach ($blocks as &$block) {

            foreach ($block['runtime_summary']['runtimes']  as $label => $timer) {
                if (!isset($runtimeSummaryAccumulate['runtimes'][$label]))
                    $runtimeSummaryAccumulate['runtimes'][$label] = ['duration' => 0];

                $runtimeSummaryAccumulate['runtimes'][$label]['duration'] += $timer['duration'];
            }

            foreach ($runtimeSummaryAccumulate['runtimes']  as $label => $timer) {
                if (!isset($block['runtime_summary_accumulate']['runtimes'][$label]))
                    $block['runtime_summary_accumulate']['runtimes'][$label] = [];

                $block['runtime_summary_accumulate']['runtimes'][$label]['duration'] = $runtimeSummaryAccumulate['runtimes'][$label]['duration'];
            }


            foreach ($block['runtime_summary']['downtimes']  as $label => $timer) {
                if ($label == 'by_id') {
                    foreach ($block['runtime_summary']['downtimes']['by_id']  as $sublabel => $subtimer) {
                        if (!isset($runtimeSummaryAccumulate['downtimes']['by_id'][$sublabel]))
                            $runtimeSummaryAccumulate['downtimes']['by_id'][$sublabel] = ['duration' => 0];

                        $runtimeSummaryAccumulate['downtimes']['by_id'][$sublabel]['duration'] += $subtimer['duration'];

                        if (!isset($block['runtime_summary_accumulate']['downtimes']['by_id'][$sublabel]))
                            $block['runtime_summary_accumulate']['downtimes']['by_id'][$sublabel] = ['duration' => 0];

                        $block['runtime_summary_accumulate']['downtimes']['by_id'][$sublabel] = ['duration' => $runtimeSummaryAccumulate['downtimes']['by_id'][$sublabel]['duration']];
                    }
                } else {
                    if (!isset($runtimeSummaryAccumulate['downtimes'][$label]))
                        $runtimeSummaryAccumulate['downtimes'][$label] = ['duration' => 0];

                    $runtimeSummaryAccumulate['downtimes'][$label]['duration'] += $timer['duration'];

                    if (!isset($block['runtime_summary_accumulate']['downtimes'][$label]))
                        $block['runtime_summary_accumulate']['downtimes'][$label] = ['duration' => 0];

                    $block['runtime_summary_accumulate']['downtimes'][$label] = ['duration' => $runtimeSummaryAccumulate['downtimes'][$label]['duration']];
                }
            }



            foreach ($runtimeSummaryAccumulate['downtimes']  as $label => $timer) {
                if ($label == 'by_id') {
                    foreach ($runtimeSummaryAccumulate['downtimes']['by_id']  as $sublabel => $subtimer) {

                        if (!isset($block['runtime_summary_accumulate']['downtimes']['by_id'][$sublabel]))
                            $block['runtime_summary_accumulate']['downtimes']['by_id'][$sublabel] = [];

                        $block['runtime_summary_accumulate']['downtimes']['by_id'][$sublabel]['duration'] = $runtimeSummaryAccumulate['downtimes']['by_id'][$sublabel]['duration'];
                    }
                } else {
                    if (!isset($block['runtime_summary_accumulate']['downtimes'][$label]))
                        $block['runtime_summary_accumulate']['downtimes'][$label] = [];

                    $block['runtime_summary_accumulate']['downtimes'][$label]['duration'] = $runtimeSummaryAccumulate['downtimes'][$label]['duration'];
                }
            }
        }


        //propagate updateHourly to production Line
        if ($updateProductionLines) {
            /** @var \App\Models\ProductionLine $productionLine */
            foreach ($this->productionLines as $productionLine) {
                $productionLine->updateHourlySummary($this->hourly_summary)->updateOverallSummary($this)->save();
            }
        }
        return $this;
    }
    /**
     * Get setup time for current production
     * @return int Setup time in seconds
     */
    private function getSetupTime()
    {
        $query = DB::connection($this->connection)->table('parts')->select([DB::raw('MAX(parts.setup_time) as max_setup_time')])
            ->join(ProductionOrder::TABLE_NAME, ProductionOrder::TABLE_NAME . '.part_id', '=', Part::TABLE_NAME . '.id')
            ->join(ProductionLine::TABLE_NAME, ProductionLine::TABLE_NAME . '.production_order_id', '=', ProductionOrder::TABLE_NAME . '.id')
            ->where(ProductionLine::TABLE_NAME . '.production_id', '=', $this->id)
            ->get();

        return $query[0]->max_setup_time ?? 0;
    }

    public function updateSetupTime()
    {
        $this->setup_time = $this->getSetupTime();
        return $this;
    }
    public function snapshotSchedule()
    {

        //Do schedule snapshot
        $scheduleData = [];

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $this->workCenter;


        //Shift Data
        /** @var \App\Models\Shift $shift */
        $shift = $workCenter->getCurrentShift();

        $productionStartTime = $this->started_at->toDateTime();

        if ($shift) {
            $scheduleData['shift_data'] = $shift->makeHidden(['plant'])->toArray();
            $scheduleData['shift_start_end'] = $shift->getLastStartEndDateTime($productionStartTime, true);

            //Break
            /** @var \App\Models\BreakSchedule $breakSchedule */
            $breakSchedule = $workCenter->breakSchedule;

            if ($breakSchedule) {
                $scheduleData['breaks'] = $breakSchedule->getBreaktimeBetween($scheduleData['shift_start_end']['start_time']->toDateTime(), $scheduleData['shift_start_end']['end_time']->toDateTime(), true);
            } else {
                $scheduleData['breaks'] = [];
            }
        }



        $this->schedule_data = $scheduleData;

        return $this;
    }

    /** @param array $downtimeEvents Plan downtime events (Event which not count up standard output) */
    private function summarizeRuntime($downtimeEvents, $startTime, $endTime)
    {
        $states = [];
        $now = new \DateTime();
        $nowTimestamp = $now->getTimestamp();

        $startTimestamp = $startTime->getTimestamp();
        $endTimestamp = null;

        if ($endTime)
            $endTimestamp = $endTime->getTimestamp();

        $states[] = [$startTimestamp, 1];

        if ($endTime && $endTime <= $now)
            $states[] = [$endTimestamp, 0];


        /** @var \App\Models\DowntimeEvent $downtimeEvent */
        foreach ($downtimeEvents as $downtimeEvent) {
            if ($downtimeEvent->end_timestamp_cache && $downtimeEvent->end_timestamp_cache <= $downtimeEvent->start_timestamp_cache) //invalid! endtime < starttime
                continue;
            if (!isValueRangeOverlap($startTimestamp, $endTimestamp, $downtimeEvent->start_timestamp_cache, $downtimeEvent->end_timestamp_cache))
                continue;

            $s = $downtimeEvent->start_timestamp_cache > $startTimestamp ? $downtimeEvent->start_timestamp_cache : $startTimestamp; //if downtime event start before startTime, pick startTime

            $states[] = [$s, 0];

            if ($downtimeEvent->end_timestamp_cache && $downtimeEvent->end_timestamp_cache <= $nowTimestamp)  //is not ongoing downtime
            {
                $e = (!$endTimestamp || $downtimeEvent->end_timestamp_cache < $endTimestamp) ? $downtimeEvent->end_timestamp_cache : $endTimestamp; //if downtime event end after endTime, pick endTime
                $states[] = [$e, 1];
            }
        }


        foreach ($states as $state) {
            if (!isset($mergedStates[$state[0]]))
                $mergedStates[$state[0]] = 0;

            if ($state[1])
                $mergedStates[$state[0]]++;
            else
                $mergedStates[$state[0]]--;
        }



        usort($states, function ($a, $b) {
            if ($a[0] == $b[0])
                return $b[1] - $a[1]; //reversed (1:0)

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





        $durationBlocks = [];
        $duration = 0;
        for ($n = 0; $n < count($end); $n++) {
            $durationBlock = $end[$n] - $start[$n];
            if ($durationBlock > 0) {
                $durationBlocks[] = $durationBlock;
                $duration += $durationBlock;
            }
        }

        if (count($start) > 0) {
            $lastBlock = $start[count($start) - 1];
            $ongoing = ($count > 0) ? $lastBlock : null;
        } else {
            $ongoing = null;
        }
        $planRuntimeSummary =  [];

        $periodData = [];
        for ($i = 0; $i < count($start); $i++) {
            $startT = $start[$i];
            $endT = $end[$i] ?? null;

            if ($startT != $endT)
                $periodData[] = [$start[$i], $end[$i] ?? null];
        }

        $planRuntimeSummary['blocks'] = $durationBlocks;
        $planRuntimeSummary['active_period'] = $periodData;
        $planRuntimeSummary['duration'] = $duration;
        $planRuntimeSummary['ongoing'] = $ongoing;

        return $planRuntimeSummary;
    }

    //Rejects
    public function setReject(array $rejectsData, WorkCenter $workCenter = null): GenericRequestResult
    {
        if (count($rejectsData) <= 0) //no data
            return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK");

        $productionLineId = $rejectsData[0]->production_line_id;

        /** @var \App\Extras\Payloads\RejectData $rejectData */
        foreach ($rejectsData as $rejectData) {
            if ($rejectData->production_line_id != $productionLineId)
                return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid Production ID");
        }

        /** @var \App\Models\ProductionLine $productionLine */
        $productionLine = $this->productionLines()->where('id', '=', $productionLineId)->first();
        if (!$productionLine)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid Production ID");

        if (!$workCenter)
            $workCenter = $this->workCenter;

        return $productionLine->setReject($rejectsData, $workCenter, $this);
    }


    //Pending
    public function setPending(PendingData $pendingData, WorkCenter $workCenter = null): GenericRequestResult
    {


        $productionLineId = $pendingData->production_line_id;


        /** @var \App\Models\ProductionLine $productionLine */
        $productionLine = $this->productionLines()->where('id', '=', $productionLineId)->first();
        if (!$productionLine)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid Production ID");

        if (!$workCenter)
            $workCenter = $this->workCenter;

        return $productionLine->setPending($pendingData, $workCenter, $this);
    }


    public function setResumeProduction(): GenericRequestResult
    {
        if (!($this->status == Production::STATUS_RUNNING ||
            $this->status == Production::STATUS_DIE_CHANGE))
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_STATUS, "Invalid status");

        //find current active breaktime
        $now = new \DateTime();
        $nowTimestamp = $now->format('Y-m-d H:i:s');
        $breakEvents = $this->downtimeEvents()
            ->where('event_type', '=', WorkCenter::DOWNTIME_STATUS_PLAN_BREAK)
            ->where('start_time', '<=', $nowTimestamp)
            ->where('end_time', '>=', $nowTimestamp)
            ->get();


        /** @var \App\Models\DowntimeEvent $breakEvent */
        foreach ($breakEvents as $breakEvent) {
            $breakEvent->end_time = $now;
            $breakEvent->save();
            $breakEvent->broadcastEndEvent(); //$breakEvent->dispatchBroadcastEndEvent();
        }

        //update plan die change event if not expired yet
        $planDieChangeEvents = $this->downtimeEvents()
            ->where('event_type', '=', WorkCenter::DOWNTIME_STATUS_PLAN_DIE_CHANGE)
            ->where('start_time', '<=', $nowTimestamp)
            ->where('end_time', '>=', $nowTimestamp)
            ->get();

        /** @var \App\Models\DowntimeEvent $planDieChangeEvent */
        foreach ($planDieChangeEvents as $planDieChangeEvent) {
            $planDieChangeEvent->end_time = $this->getDieChangeExpireTime();
            $planDieChangeEvent->save();
            $planDieChangeEvent->dispatchBroadcastEndEvent();
        }

        $this->workCenter->sendToOpc(OpcTagType::TAG_BREAK, 0);
        return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK");
    }

    public function setBreakProduction(): GenericRequestResult
    {
        if (!($this->status == Production::STATUS_RUNNING ||
            $this->status == Production::STATUS_DIE_CHANGE))
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_STATUS, "Invalid status");

        //find current active breaktime
        $now = new \DateTime();
        $nowTimestamp = $now->format('Y-m-d H:i:s');
        $breakEvent = $this->downtimeEvents()
            ->where('event_type', '=', WorkCenter::DOWNTIME_STATUS_PLAN_BREAK)
            ->where('start_time', '<=', $nowTimestamp)
            ->where('end_time', '>=', $nowTimestamp)
            ->first();

        if ($breakEvent) //Already in break
            return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK");

        //Check in breaktime
        $breakTimeSlots = $this->schedule_data['breaks'];
        $breakSlot = null;
        foreach ($breakTimeSlots as &$breakTime) { //cast to datetime
            $breakTime['start_time'] = new \DateTime($breakTime['start_time']);
            $breakTime['end_time'] = new \DateTime($breakTime['end_time']);
            if ($breakTime['start_time'] <=  $now && $breakTime['end_time'] >  $now) {
                $breakSlot = $breakTime;
            }
        }

        if ($breakSlot) {
            $breakTimeEvent = new DowntimeEvent();
            $breakTimeEvent->production_id = $this->id;
            $breakTimeEvent->event_type = WorkCenter::DOWNTIME_STATUS_PLAN_BREAK;
            $breakTimeEvent->start_time = $now;
            $breakTimeEvent->end_time = $breakTime['end_time'];
            $breakTimeEvent->setConnection($this->connection)->save();
            $breakTimeEvent->broadcastStartEvent() //$breakTimeEvent->dispatchBroadcastStartEvent() //trigger broadcast on start event
                ->dispatchBroadcastEndEvent(); //trigger broadcast on end event

            $this->workCenter->sendToOpc(OpcTagType::TAG_BREAK, 1);
        }


        //update plan die change event if not expired yet
        $planDieChangeEvents = $this->downtimeEvents()
            ->where('event_type', '=', WorkCenter::DOWNTIME_STATUS_PLAN_DIE_CHANGE)
            ->where('start_time', '<=', $nowTimestamp)
            ->where('end_time', '>=', $nowTimestamp)
            ->get();

        /** @var \App\Models\DowntimeEvent $planDieChangeEvent */
        foreach ($planDieChangeEvents as $planDieChangeEvent) {
            $planDieChangeEvent->end_time = $this->getDieChangeExpireTime();
            $planDieChangeEvent->save();
            $planDieChangeEvent->dispatchBroadcastEndEvent();
        }

        return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK");
    }


    public function setDowntimeReason(DowntimeReasonData $reasonData): GenericRequestResult
    {
        if ($reasonData->production_id != $this->id)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, 'Invalid Production ID');

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $this->workCenter;
        if (!$workCenter)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, 'Invalid Parameter');

        /** @var \App\Models\WorkCenterDowntime $workCenterDowntime */
        $workCenterDowntime = $workCenter->workCenterDowntimes()->where('downtime_id', '=', $reasonData->downtime_id)->first();
        if (!$workCenterDowntime)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, 'Invalid Downtime ID');

        /** @var \App\Models\Downtime $downtime */
        $downtime = $workCenterDowntime->downtime;
        if (!$downtime)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, 'Invalid Downtime');


        /** @var \App\Models\DowntimeReason $downtimeReason */
        $downtimeReason = $downtime->downtimeReasons()->where(DowntimeReason::TABLE_NAME . '.id', $reasonData->downtime_reason_id)->first();
        if (!$downtimeReason)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, 'Invalid Downtime Reason');

        /** @var \App\Models\DowntimeEvent $activeDowntimeEvent */
        $activeDowntimeEvent = $this->downtimeEvents()
            ->where('downtime_id', '=', $reasonData->downtime_id)
            ->whereNull('end_time')->first();

        if (!$activeDowntimeEvent)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, 'Downtime Not Active');

        $activeDowntimeEvent->reason = $downtimeReason->reason;
        $activeDowntimeEvent
            ->setDowntimeReason(
                $downtimeReason->reason,
                ($downtimeReason->enable_user_input ? $reasonData->user_input_reason : null),
                $reasonData->user_id
            )->save();

        try {
            event(new WorkCenterDowntimeStateChangeEvent($workCenter));
        } catch (\Exception $ex) {
        }

        return new GenericRequestResult(GenericRequestResult::RESULT_OK, 'OK');
    }


    public function getActiveDowntimeEvent(): array
    {
        $nowTimestamp = date('Y-m-d H:i:s');

        return $this->downtimeEvents()->where('start_time', '<=', $nowTimestamp)->where(
            function (Builder $q) use ($nowTimestamp) {
                $q->whereNull('end_time')
                    ->orWhere('end_time', '>', $nowTimestamp);
            }
        )->get()->toArray();
    }
    //relationships

    //belongto work_center_id
    public function workCenter()
    {
        return $this->belongsTo(WorkCenter::class, 'work_center_id', 'id');
    }

    //belongto user_id
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    //belongto shift_type_id
    public function shiftType()
    {
        return $this->belongsTo(ShiftType::class, 'shift_type_id', 'id');
    }

    //hasmany production_lines
    public function productionLines()
    {
        return $this->hasMany(ProductionLine::class, 'production_id', 'id');
    }
    //hasmanythrough production_orders
    public function productionOrders()
    {
        return $this->hasManyThrough(ProductionOrder::class, ProductionLine::class, 'production_id', 'id', 'id', 'production_order_id');
    }

    //hasmany downtimeevents
    public function downtimeEvents()
    {
        return $this->hasMany(DowntimeEvent::class, 'production_id', 'id');
    }
}
