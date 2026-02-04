<?php

namespace App\Models;

use Exception;
use App\Jobs\SendToOpc;
use App\Jobs\PlanDieChangeExpired;
use App\Jobs\UpdateDieChangeEvent;
use Illuminate\Support\Facades\DB;
use App\Extras\Payloads\RejectData;
use App\Extras\Payloads\ReworkData;
use Illuminate\Support\Facades\Log;
use App\Extras\Payloads\PendingData;
use App\Extras\Support\DieChangeInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Events\Terminal\StartDieChangeEvent;
use App\Events\Terminal\CancelDieChangeEvent;
use App\Extras\Payloads\GenericRequestResult;
use App\Events\Terminal\WorkCenterDataUpdateEvent;
use App\Events\Terminal\WorkCenterStateChangeEvent;
use App\Events\Terminal\WorkCenterReworkUpdateEvent;
use App\Events\Terminal\CancelFirstProductConfirmation;
use App\Events\Terminal\WorkCenterDowntimeStateChangeEvent;
use App\Events\Terminal\CancelFirstProductConfirmationEvent;
use App\Events\Terminal\ProceedFirstProductConfirmationEvent;
use App\Extras\Payloads\DowntimeData;
use App\Extras\Support\ModelDestroyable;
use App\Jobs\BroadcastWorkCenterDataUpdateEvent;
use App\Jobs\SignalOpcSettingUpdated;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;

/**
 * Database Columns
 * 
 * @property int $id Primary key
 * 
 * @property int $factory_id Foreign key (factory): index nullable
 * @property int $dashboard_layout_id Foreign key (dashboardLayout): index nullable
 * @property int $current_production_id Foreign key (productions): index nullable
 * 
 * @property string $uid string index
 * @property string $name string
 * @property string $production_line_count string
 * @property int $status tinyinteger (0: IDLE, 1: DIE_CHANGE, 2: FIRST_CONFIRMATION, 3: RUNNING)
 * @property int $downtime_state tinyinteger (0: No Downtime, 1: Human Downtime, 2: Machine Downtime, 3: Die-Change, 4: Break)
 * 
 * @property int $enabled tinyinteger (0: disabled, 1: enabled)
 * 
 * @property string $pps_source string
 * @property string $gr_ok_destination string
 * @property string $gr_ng_destination string
 * @property string $gr_qi_destination string
 * @property string $rw_ok_destination string
 * @property string $rw_ng_destination string
 * @property string $ett10_destination string
 * @property string $ett20_destination string
 * 
 * @property string $created_at timestamp
 * @property string $updated_at timestamp
 * @property int $last_broadcast_update
 */


class WorkCenter extends Model implements ModelDestroyable
{
    /** Work Center Idle */
    const STATUS_IDLE = 0; //PPS 
    /** Work Center Die Change */
    const STATUS_DIE_CHANGE = 1; //DIE CHANGE
    /** Work Center First Product Confirmation */
    const STATUS_FIRST_CONFIRMATION = 2; //DIE CHANGE
    /** Work Center Running */
    const STATUS_RUNNING = 3; //PROGRESS,REJECT,DOWNTIME,PENDING

    /** No Downtime */
    const DOWNTIME_STATUS_NONE = 0;

    /** Unplanned Downtime: Human */
    const DOWNTIME_STATUS_UNPLAN_HUMAN = -1;
    /** Unplanned Downtime: Machine */
    const DOWNTIME_STATUS_UNPLAN_MACHINE = -2;
    /** Unplanned Downtime: Die-Change */
    const DOWNTIME_STATUS_UNPLAN_DIE_CHANGE = -3;

    /** Planned Downtime: Die-Change */
    const DOWNTIME_STATUS_PLAN_DIE_CHANGE = 3;
    /** Planned Downtime: Break */
    const DOWNTIME_STATUS_PLAN_BREAK = 4;




    const DIE_CHANGE_LOT_COUNT = 6;


    const TABLE_NAME = 'work_centers';
    protected $table = self::TABLE_NAME;

    protected $hidden = [
        'pps_source',
        'gr_ok_destination',
        'gr_ng_destination',
        'gr_qi_destination',
        'rw_ok_destination',
        'rw_ng_destination',
        'ett10_destination',
        'ett20_destination'
    ];
    public function releaseOpcActiveTagsSetValue()
    {
        //TEMP function before rework opc tagging
        $opcTags = $this->opcTags()->get();

        /** @var \App\Models\OpcTag $opcTag */
        foreach ($opcTags as $opcTag) {


            /** @var \App\Models\OpcServer $opcServer */
            $opcServer =  $opcTag->setConnection(null)->opcServer()->first();
            if (!$opcServer)
                continue;



            /** @var \App\Models\OpcActiveTag $opcActiveTag */
            $opcActiveTag = $opcServer->opcActiveTags()->where('tag', $opcTag->tag)->first();

            if (!$opcActiveTag)
                continue;


            $opcActiveTag->set_value = null;
            $opcActiveTag->save();
        }

        dispatch(new SignalOpcSettingUpdated());
    }

    //Utils
    public function syncPpsFromDirectory($autoArchive = true): self
    {
        //try read pps from directory & store new pps into database

        /** @var \App\Models\Plant $plant */
        $plant = $this->plant;

        if (!$plant)
            return $this;

        $sheets = ProductionOrder::getFromPath($plant, $this->pps_source);



        /**  @var \App\Models\ProductionOrder $sheet */
        foreach ($sheets as $sheet) {

            if (!$sheet->work_center_id) //pps line entry not equal to work center, ignore
                continue;

            $sheet->setConnection($this->getConnectionName());
            if (!$sheet->getRecordFromDatabase() && !is_null($sheet->plant_id)) {
                $sheet->save();
            }
        }
        if ($autoArchive) {
            $pathSource = $this->pps_source;
            if ($pathSource[-1] != DIRECTORY_SEPARATOR)
                $pathSource .= DIRECTORY_SEPARATOR;

            $archivePath = $pathSource . 'archive' . DIRECTORY_SEPARATOR;
            try {
                mkdir($archivePath);
            } catch (Exception $ex) {
            }


            foreach ($sheets as $sheet) {
                //move to archieve
                $fileNameParts = explode(DIRECTORY_SEPARATOR, $sheet->pps_filename);

                if (count($fileNameParts) <= 0)
                    continue;

                $fileName = trim($fileNameParts[count($fileNameParts) - 1]);
                if (strlen($fileName) <= 0)
                    continue;

                $filePath = $pathSource . $fileName;

                if (!file_exists($filePath)) {
                    continue;
                }

                $n = 0;
                $retryCount = 0;
                if (file_exists($filePath)) {
                    $archiveFilePath = $archivePath . $fileName;
                    while (file_exists($archiveFilePath) && $retryCount < 1000) {
                        $archiveFilePath = $archivePath  . $fileName . '_' . (++$n);
                        $retryCount++;
                    }
                    if (file_exists($archiveFilePath)) {
                        Log::warning('Unable to archive PPS (' . $filePath . '): Too many duplicate filename in archive');
                        continue;
                    }

                    try {
                        rename($filePath, $archiveFilePath);
                    } catch (Exception $ex) {
                        Log::warning('Unable to archive PPS (' . $filePath . '): ' . $ex->getMessage());
                    }
                }
            }
        }

        return $this;
    }
    public function resolvePpsPartNotFound()
    {
        //Resolve PPS missing parts
        $missingParts = $this->productionOrders()->whereNull('production_orders.part_id')->where('production_orders.status', '=', 0)->get();
        foreach ($missingParts as $pps) {
            if (!$pps->pps_part_no || strlen($pps->pps_part_no) <= 0)
                continue;

            $part = Part::on($this->getConnectionName())
                ->where('plant_id', '=', $this->plant->id)
                ->where('work_center_id', '=', $this->id)
                ->where('part_no', '=', $pps->pps_part_no)->first();

            if (!$part)
                continue;

            $pps->part_id = $part->id;
            $pps->save();
        }
    }
    public function updateOpcTagCacheValues()
    {
        //update opcTag value from activeOpcTag
        $tags = $this->opcTags()->get();

        /** @var \App\Models\OpcTag $tag */
        foreach ($tags as $tag) {
            $tag->syncFromActiveTag();
        }
    }
    public function getCurrentPartTagID(int $lineNo)
    {
        /** @var \App\Models\OpcTag $opcTag */
        $opcTag = $this->opcTags()->where('opc_tag_type_id', OpcTagType::TAG_PART_NUMBER)->where('info', $lineNo)->first();
        if (!$opcTag)
            return null;

        return $opcTag->value;
    }
    public function startDieChange(array $productionOrderIds, bool $ignorePartNumberCheck = false, User $user = null): GenericRequestResult
    {

        $user =  $user ?? User::getCurrent() ?? null;
        if (!$user)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid parameters"); //critical error, user should not be null

        //VALIDATION 

        //Check Work Center Idle
        if ($this->status !== self::STATUS_IDLE) { //work center not idle
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_STATUS, "Invalid work center status");
        }

        //Check Productions
        if (count($productionOrderIds) <= 0) { //no production order selected
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "No production order selected");
        }

        if (!$productionOrderIds) { //invalid production_orders parameter
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid input");
        }

        /** @var \App\Models\Shift $currentShift */
        $currentShift = $this->getCurrentShift();

        if (!$currentShift) //check in shift
        {
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_STATUS, "No shift");
        }
        $startEndShift = $currentShift->getLastStartEndDateTime();
        $productionOrders = [];
        foreach ($productionOrderIds as $productionOrderId) {
            $productionOrder = $this->productionOrders()->find($productionOrderId);

            if (!$productionOrder || !($productionOrder->status === ProductionOrder::STATUS_PPS || $productionOrder->status === ProductionOrder::STATUS_INCOMPLETE))
                return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid production order selected");

            $productionOrders[] = $productionOrder;
        }

        //Check part number
        $invalidPartNumbers = [];
        $lines = [];
        /** @var \App\Models\ProductionOrder $productionOrder */
        foreach ($productionOrders as $productionOrder) {
            /** @var \App\Models\Part $part */
            $part = $productionOrder->part;
            if (!$part)
                return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid Part");

            if (in_array($part->line_no, $lines))
                return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Duplicate line no");

            if ($part->line_no <= 0 || $part->line_no > $this->production_line_count)
                return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid line no");

            $lines[] = $part->line_no;
            $currentPartTagId = $this->getCurrentPartTagID($part->line_no);
            if ($part->opc_part_id != $currentPartTagId)
                $invalidPartNumbers[] = ['part' => $part, 'value' => $currentPartTagId];
        }

        if (count($invalidPartNumbers) && !$ignorePartNumberCheck) {

            usort($invalidPartNumbers, function ($a, $b) {
                return $a['part']->line_no <=> $b['part']->line_no;
            });

            $message = "";
            foreach ($invalidPartNumbers as $invalid) {
                /** @var \App\Models\Part $part */
                $part = $invalid['part'];
                $message .= 'Line No: ' . $part->line_no . ', ';
                $message .= 'Part Tag ID: ' . $part->opc_part_id . ', ';
                $message .= 'Set Tag ID: ' . $invalid['value'] . "\r\n";
            }
            return new GenericRequestResult(GenericRequestResult::RESULT_RESTRICTED, "Part Tag ID not matched!\r\n" . $message);
        }



        //Start Die Change 


        //Create Production
        $newProduction = new Production([
            'user_id' => $user->id ?? null,
            'work_center_id' => $this->id,
            'status' => Production::STATUS_DIE_CHANGE,
            'die_change_info' => $this->generateDieChangeInfoTemplate(),
            'shift_type_id' => $currentShift->shift_type_id,
            'shift_date' => $startEndShift['start_time']->format('Y-m-d'), //Local time date
        ]);



        $newProduction->setConnection($this->getConnectionName());
        $newProduction->started_at = date('Y-m-d H:i:s');
        $newProduction->stopped_at = $startEndShift['end_time']->setTimeZone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');
        $newProduction->snapshotSchedule(); //snapshot schedule (Shift & Break)
        $newProduction->hourly_summary = [];
        $newProduction->save();

        /** @var \App\Models\ProductionOrder $productionOrder */
        foreach ($productionOrders as $productionOrder) {

            $productionLine = $productionOrder->createProductionLine($newProduction);
            $productionLine->save();

            //create initial counter log

            $opcTag = $this->opcTags()
                ->where('opc_tag_type_id', '=', OpcTagType::TAG_COUNTER)
                ->where('info', '=', $productionLine->line_no)->first();

            if (!$opcTag)
                continue;

            $counterLog = new CounterLog();
            $counterLog->work_center_id = $this->id;
            $counterLog->opc_tag_id = $opcTag->id;
            $counterLog->production_line_id = $productionLine->id;
            $counterLog->line_no = $productionLine->line_no;
            $counterLog->tag_value = $opcTag->value;
            $counterLog->work_center_status = WorkCenter::STATUS_IDLE;
            $counterLog->recorded_at = date('Y-m-d H:i:s');
            $counterLog->setConnection($this->connection)->save();
        }

        $newProduction->updateSetupTime()->save(); //update to longest setup time


        //Set production to work center
        $this->current_production_id = $newProduction->id;
        $this->save();

        //Set status to Die Change (Production & Production Orders)

        /** @var \App\Models\Production $currentProduction */
        $currentProduction = $this->currentProduction()->first();

        $this->resetAllHumanDowntimes();
        $currentProduction->startDieChange();

        $this->setStatus(self::STATUS_DIE_CHANGE)->save(); //$this->status = self::STATUS_DIE_CHANGE;


        $currentProduction->updateHourlySummary()->save();
        $this->updateDowntimeState()->save();


        //$currentProduction->updateHourlySummary()->save();

        $this->sendToOpc(OpcTagType::TAG_DIE_CHANGE, 1);
        $this->sendToOpc(OpcTagType::TAG_ON_PRODUCTION, 1);

        $this->checkBreakSignal();

        event(new StartDieChangeEvent($this, $this->currentProduction, $user));

        $this->broadcastWorkCenterDataUpdate();

        return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK");
    }

    public function closeAllDowntimeEvent(\DateTime $eventEndTime = null)
    {
        //close all event when production ended/stopped/cancel (detaching from workcenter)

        if (!$eventEndTime)
            $eventEndTime = new \DateTime();

        /** @var \App\Models\Production $currentProduction */
        $currentProduction = $this->currentProduction;
        if (!$currentProduction)
            return $this;

        if (!$currentProduction->die_change_end_at) {
            $currentProduction->die_change_end_at = $eventEndTime;
            $currentProduction->save();
        }

        //event clean-up
        //unclosed die-change
        /** @var \App\Models\DowntimeEvent $pendingDieChangeEvent */
        $pendingDieChangeEvent = $currentProduction->downtimeEvents()
            ->where('event_type', '=', WorkCenter::DOWNTIME_STATUS_PLAN_DIE_CHANGE)
            ->whereNull('end_time')
            ->first();

        if ($pendingDieChangeEvent) {
            $pendingDieChangeEvent->processDieChangeEvent();
        }


        $downtimeEvents = $currentProduction->downtimeEvents()->where(function (Builder $q) use ($eventEndTime) {
            $q->where('end_time', '>', $eventEndTime) //endtime exceed eventEndTime limit
                ->orWhereNull('end_time'); //unclosed
        })->get();

        foreach ($downtimeEvents as $downtimeEvent) {
            $downtimeEvent->end_time = $eventEndTime;
            $downtimeEvent->save();
        }

        return $this;
    }

    //Trigger on start die change & stop / cancel production
    public function resetAllHumanDowntimes()
    {
        $pivot = WorkCenterDowntime::TABLE_NAME;
        DB::connection($this->connection)->table($pivot)
            ->join(Downtime::TABLE_NAME, $pivot . '.downtime_id', '=', Downtime::TABLE_NAME . '.id')
            ->where(Downtime::TABLE_NAME . '.downtime_type_id', '=', 2) // 2= Human downtime
            ->update([$pivot . '.state' => 0, $pivot . '.value_updated_at' => date('Y-m-d H:i:s')]);

        $this->sendToOpc(OpcTagType::TAG_HUMAN_DOWNTIME, 0);

        return $this;
    }

    public function updateDowntimeState($forceTriggerDataUpdatedEvent = false)
    {
        $currentDowntimeState = $this->downtime_state;

        if ($this->status == $this::STATUS_IDLE) {

            //idle, use directly from tag, no event generated

            $activeDowntimes = $this->downtimes()->wherePivot('state', '=', '1')->get()->all();

            $machineDowntime = false;
            $humanDowntime = false;

            /** @var \App\Models\Downtime $activeDowntime */
            foreach ($activeDowntimes as $activeDowntime) {
                $machineDowntime |= ($activeDowntime->downtime_type_id == DowntimeType::MACHINE_DOWNTIME);
                $humanDowntime |=  ($activeDowntime->downtime_type_id == DowntimeType::HUMAN_DOWNTIME);
            }

            if ($machineDowntime && $humanDowntime) {
                //pick higher priority
                $map = DowntimeEvent::getPriorityMap();
                if ($map[WorkCenter::DOWNTIME_STATUS_UNPLAN_MACHINE] < $map[WorkCenter::DOWNTIME_STATUS_UNPLAN_HUMAN])
                    $this->downtime_state = WorkCenter::DOWNTIME_STATUS_UNPLAN_MACHINE;
                else
                    $this->downtime_state = WorkCenter::DOWNTIME_STATUS_UNPLAN_HUMAN;
            } elseif ($machineDowntime)
                $this->downtime_state = WorkCenter::DOWNTIME_STATUS_UNPLAN_MACHINE;
            elseif ($humanDowntime)
                $this->downtime_state = WorkCenter::DOWNTIME_STATUS_UNPLAN_HUMAN;
            else
                $this->downtime_state = WorkCenter::DOWNTIME_STATUS_NONE;
        } else {
            //Check current downtime

            if ($this->current_production_id) {
                $now = date('Y-m-d H:i:s');

                //Get ongoing downtimes
                $downtimeEvents = DowntimeEvent::on($this->connection)
                    ->where('production_id', '=', $this->current_production_id)
                    ->where('start_time', '<=', $now)
                    ->where(function (Builder $q) use ($now) {
                        $q->where('end_time', '>', $now)
                            ->orWhereNull('end_time');
                    })
                    ->get()->all();

                if (count($downtimeEvents) > 0) {

                    usort($downtimeEvents, [DowntimeEvent::class, 'sortByPriority']);
                    $this->downtime_state = $downtimeEvents[0]->event_type;
                    // dd($downtimeEvents[0]);
                } else
                    $this->downtime_state = WorkCenter::DOWNTIME_STATUS_NONE;
            }
        }

        if ($forceTriggerDataUpdatedEvent || $currentDowntimeState != $this->downtime_state) {

            //broadcast Workcenter Updated
            $this->save();
            $this->broadcastWorkCenterDataUpdate();
        }

        return $this;
    }

    public function setCancelDieChange(User $user = null): GenericRequestResult
    {
        $user =  $user ?? User::getCurrent() ?? null;
        if (!$user)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid parameters"); //critical error, user should not be null

        if ($this->status !== self::STATUS_DIE_CHANGE) { //work center in die-change state
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_STATUS, "Invalid work center status");
        }


        //cancel all planning
        /** @var \App\Models\Production $canceledProduction */
        $canceledProduction = $this->currentProduction;

        //set status canceled
        $canceledProduction->cancelProduction();
        $this->setStatus(self::STATUS_IDLE)->save(); //$this->status = self::STATUS_IDLE;

        $this->closeAllDowntimeEvent();
        $this->currentProduction()->dissociate();
        $this->save();

        $this->resetAllHumanDowntimes();

        // Already updatehourlysummary at ->cancelProduction() $canceledProduction->updateHourlySummary()->save();
        $this->updateDowntimeState()->save();

        $this->sendToOpc(OpcTagType::TAG_DIE_CHANGE, 0);
        $this->sendToOpc(OpcTagType::TAG_ON_PRODUCTION, 0);

        event(new CancelDieChangeEvent($this, $canceledProduction, $user));

        //broadcast Workcenter Updated
        $this->broadcastWorkCenterDataUpdate();



        return new GenericRequestResult(GenericRequestResult::RESULT_OK, 'OK');
    }

    /**
     * Return back to Die change state
     */
    public function setCancelFirstProductConfirmation(User $user = null): GenericRequestResult
    {
        $user =  $user ?? User::getCurrent() ?? null;
        if (!$user)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid parameters"); //critical error, user should not be null

        if ($this->status !== self::STATUS_FIRST_CONFIRMATION) { //work center in die-change state
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_STATUS, "Invalid work center status");
        }

        //cancel first product confirmation
        $canceledProduction = $this->currentProduction;

        //set status die change
        $this->setStatus(self::STATUS_DIE_CHANGE)->save(); //$this->status = self::STATUS_DIE_CHANGE;
        //$this->save();

        $this->sendToOpc(OpcTagType::TAG_DIE_CHANGE, 1); //back to die change page
        event(new CancelFirstProductConfirmationEvent($this, $canceledProduction, $user));


        //broadcast Workcenter Updated
        $this->broadcastWorkCenterDataUpdate();



        return new GenericRequestResult(GenericRequestResult::RESULT_OK, 'OK');
    }

    public function checkBreakSignal()
    {
        if ($this->downtime_state == WorkCenter::DOWNTIME_STATUS_PLAN_BREAK)
            $this->sendToOpc(OpcTagType::TAG_BREAK, 1);
        else
            $this->sendToOpc(OpcTagType::TAG_BREAK, 0);

        return $this;
    }

    public function setFirstProductConfirmation(): GenericRequestResult
    {
        $user =  $user ?? User::getCurrent() ?? null;
        if (!$user)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid parameters"); //critical error, user should not be null


        $this->setStatus(self::STATUS_FIRST_CONFIRMATION)->save(); //$this->status = self::STATUS_FIRST_CONFIRMATION;
        //$this->save();

        $this->sendToOpc(OpcTagType::TAG_DIE_CHANGE, 0);
        event(new ProceedFirstProductConfirmationEvent($this, $this->currentProduction, $user));

        //broadcast Workcenter Updated
        $this->broadcastWorkCenterDataUpdate();

        return new GenericRequestResult(GenericRequestResult::RESULT_OK, 'OK');
    }


    public function setStartProduction(): GenericRequestResult
    {
        $user =  $user ?? User::getCurrent() ?? null;
        if (!$user)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid parameters"); //critical error, user should not be null

        /** @var \App\Models\Production $currentProduction */
        $currentProduction = $this->currentProduction;

        $currentProduction->startProduction();
        $this->setStatus(self::STATUS_RUNNING)->save();

        // Already updatehourlysummary at ->startProduction() $currentProduction->updateHourlySummary()->save();
        $this->updateDowntimeState()->save();
        event(new GenericRequestResult($this, $this->currentProduction, $user));

        //broadcast Workcenter Updated
        $this->broadcastWorkCenterDataUpdate();



        return new GenericRequestResult(GenericRequestResult::RESULT_OK, 'OK');
    }

    public function setStopProduction($forced = false): GenericRequestResult
    {

        $user =  $user ?? User::getCurrent() ?? null;
        if (!$user && !$forced)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid parameters"); //critical error, user should not be null

        if ($this->status !== self::STATUS_RUNNING && !$forced) { //work center in die-change state
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_STATUS, "Invalid work center status");
        }


        //stop production
        $stopProduction = $this->currentProduction;

        //set status idle
        /** @var \App\Models\Production $currentProduction */
        $currentProduction = $this->currentProduction;

        if ($currentProduction)
            $currentProduction->stopProduction();
        $this->setStatus(self::STATUS_IDLE)->save(); //$this->status = self::STATUS_IDLE;


        $this->closeAllDowntimeEvent();
        if ($currentProduction)
            $this->currentProduction()->dissociate();

        $this->save();
        $this->resetAllHumanDowntimes();
        // Already updatehourlysummary at ->stopProduction() $currentProduction->updateHourlySummary()->save();
        $this->updateDowntimeState()->save();

        //off all signal
        $this->sendToOpc(OpcTagType::TAG_DIE_CHANGE, 0);
        $this->sendToOpc(OpcTagType::TAG_BREAK, 0);
        $this->sendToOpc(OpcTagType::TAG_ON_PRODUCTION, 0);

        event(new GenericRequestResult($this, $stopProduction, $user));

        //Broadcast workcenter updated
        $this->broadcastWorkCenterDataUpdate();



        return new GenericRequestResult(GenericRequestResult::RESULT_OK, 'OK');
    }

    public function setStatus($newStatus)
    {

        if ($this->status != $newStatus) {
            $previousStatus = $this->status;
            $this->status = $newStatus;
            $this->save();

            try {
                event(new WorkCenterStateChangeEvent($this, $previousStatus, User::getCurrent() ?? null));
            } catch (Exception $ex) {
            }
        }

        return $this;
    }


    /** Resume workcenter when on break */
    public function setResumeProduction(): GenericRequestResult
    {
        /** @var \App\Models\Production $production */
        $production = $this->currentProduction;

        if (!$production)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_STATUS, "No production running"); //No production running


        return $production->setResumeProduction();
    }

    /** Set workcenter break */
    public function setBreakProduction(): GenericRequestResult
    {
        /** @var \App\Models\Production $production */
        $production = $this->currentProduction;

        if (!$production)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_STATUS, "No production running"); //No production running


        return $production->setBreakProduction();
    }

    /** Generate Die Change info template */
    public function generateDieChangeInfoTemplate()
    {
        $dieChangeInfo = new DieChangeInfo();
        $dieChangeInfo->lot_count = self::DIE_CHANGE_LOT_COUNT;
        $dieChangeInfo->man_power = 0;

        $dieChangeInfo->material_part = [];
        $dieChangeInfo->child_part = [];
        $dieChangeInfo->coil_bar = [];

        for ($n = 0; $n < $dieChangeInfo->lot_count; $n++) {
            $dieChangeInfo->material_part[] = null;
            $dieChangeInfo->child_part[] = null;
            $dieChangeInfo->coil_bar[] = null;
        }

        return $dieChangeInfo;
    }

    public function getCurrentShift(): Shift | null
    {
        /** @var \App\Models\Plant $plant */
        $plant = $this->plant;

        $shifts = $plant->onPlantDb()->shift()->where('enabled', '=', 1)->get();

        /** @var \App\Models\Shift $shift */
        foreach ($shifts as $shift) {
            if ($shift->isInsideShift())
                return $shift;
        }

        return null;
    }


    public function broadcastWorkCenterReworkUpdate(ProductionLine $productionLine)
    {
        //broadcast Workcenter Rework Updated
        try {
            event(new WorkCenterReworkUpdateEvent($this, $productionLine));
        } catch (Exception $ex) {
        }
    }
    public function broadcastWorkCenterDataUpdate()
    {
        //broadcast Workcenter Updated
        dispatch(new BroadcastWorkCenterDataUpdateEvent($this));
    }

    //Rejects
    public function setReject(array $rejectsData): GenericRequestResult
    {
        if (count($rejectsData) <= 0) //no data
            return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK");

        /** @var \App\Models\Production $currentProduction */
        $currentProduction = $this->currentProduction;
        if (!$currentProduction)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_STATUS, "Invalid Status");

        return $currentProduction->setReject($rejectsData, $this);
        //return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK");
    }


    //Pending
    public function setPending(PendingData $pendingData): GenericRequestResult
    {

        /** @var \App\Models\Production $currentProduction */
        $currentProduction = $this->currentProduction;
        if (!$currentProduction)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_STATUS, "Invalid Status");

        return $currentProduction->setPending($pendingData, $this);
        //return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK");
    }

    //Rework
    public function setRework(ReworkData $reworkData): GenericRequestResult
    {
        /** @var \App\Models\ProductionLine $productionLine */
        $productionLine = $this->productionLines()->where(ProductionLine::TABLE_NAME . '.id', $reworkData->production_line_id)->first();
        if (!$productionLine)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid Production Line");

        return $productionLine->setRework($reworkData, $this);
        //return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK");
    }

    //Downtime
    public function setDowntime(DowntimeData $downtimeData): GenericRequestResult
    {
        /** @var \App\Models\Production $production */
        $production = $this->currentProduction;

        if (!$production) {
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_STATUS, "No production running");
        }

        return $production->setDowntime($downtimeData);
    }
    public function checkHumanDowntimeTag()
    {

        // $workCenterDowntimes = $this->workCenterDowntimes()->with('downtime')->get();
        $val = DB::connection($this->connection)
            ->table(WorkCenterDowntime::TABLE_NAME)
            ->select([DB::raw('SUM(`' . WorkCenterDowntime::TABLE_NAME . '`.`state`) as count')])
            ->join(Downtime::TABLE_NAME, Downtime::TABLE_NAME . '.id', '=', WorkCenterDowntime::TABLE_NAME . '.downtime_id')
            ->where(WorkCenterDowntime::TABLE_NAME . '.work_center_id', '=', $this->id)
            ->where(
                Downtime::TABLE_NAME . '.downtime_type_id',
                '=',
                DowntimeType::HUMAN_DOWNTIME
            )->first();

        $this->sendToOpc(OpcTagType::TAG_HUMAN_DOWNTIME, $val->count > 0 ? 1 : 0);
    }
    public function closeRework(int $productionLineId)
    {
        /** @var \App\Models\ProductionLine $productionLine */
        $productionLine = $this->productionLines()->where(ProductionLine::TABLE_NAME . '.id', $productionLineId)->first();
        if (!$productionLine)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid Production Line");

        return $productionLine->closeRework($this);
    }

    public function sendToOpc($tag_type, $value, $line_no = null)
    {
        $allowedTagTypes = [
            OpcTagType::TAG_DIE_CHANGE,
            OpcTagType::TAG_BREAK,
            OpcTagType::TAG_HUMAN_DOWNTIME,
            OpcTagType::TAG_ON_PRODUCTION,
        ];

        if (!in_array($tag_type, $allowedTagTypes))
            return false;

        $opcTag = $this->opcTags()->where('opc_tag_type_id', $tag_type)->first();

        if (!$opcTag)
            return false;

        /** @var \App\Models\Plant $plant */
        $plant = $this->plant;

        if (!$plant)
            return false;

        /** @var \App\Models\OpcServer $opcServer */
        $opcServer = $plant->onMainDb()->opcServers()->where(OpcServer::TABLE_NAME . '.id', '=', $opcTag->opc_server_id)->first();

        if (!$opcServer)
            return;

        //set value to active opc tag

        /** @var \App\Models\OpcActiveTag $opcActiveTag */

        $opcActiveTag = $opcServer->opcActiveTags()->where('tag', $opcTag->tag)->first();
        if ($opcActiveTag) {
            Log::info("Changing Tag Value " . $opcActiveTag->tag . ' - ' . $opcActiveTag->set_value . ' => ' . $value);
            $opcActiveTag->set_value = $value;
            $opcActiveTag->save();
        }


        dispatch(new SendToOpc($this, $opcTag, $value));
        return true;
    }

    public function isDestroyable(string &$reason = null): bool
    {
        //TODO, only return true when no other resource references to this


        return false;
    }

    //relationships

    //belongto plant_id
    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plant_id', 'id');
        //return $this->hasOneThrough(Plant::class, Factory::class, 'id', 'id', 'factory_id', 'plant_id');
    }

    //belongto dashboard_layouts_id
    public function dashboardLayout()
    {
        return $this->belongsTo(DashboardLayout::class, 'dashboard_layout_id', 'id');
    }

    //hasmany productions
    public function productions()
    {
        return $this->hasMany(Production::class, 'work_center_id', 'id');
    }

    //belongsTo
    public function currentProduction()
    {
        return $this->belongsTo(Production::class, 'current_production_id', 'id');
    }

    public function productionLines()
    {
        return $this->hasManyThrough(ProductionLine::class, Production::class, 'work_center_id', 'production_id', 'id', 'id');
    }

    //hasmany counter_logs
    public function counterLogs()
    {
        return $this->hasMany(CounterLog::class, 'work_center_id', 'id');
    }

    //hasmany downtime_state_logs
    public function downtimeStateLogs()
    {
        return $this->hasMany(DowntimeStateLog::class, 'work_center_id', 'id');
    }

    //hasmany opc_tags
    public function opcTags()
    {
        return $this->hasMany(OpcTag::class, 'work_center_id', 'id');
    }

    //hasmany productionplanningsheet
    public function productionOrders()
    {
        return $this->hasMany(ProductionOrder::class, 'work_center_id', 'id');
    }

    //hasmany parts
    public function parts()
    {
        return $this->hasMany(Part::class, 'work_center_id', 'id');
    }

    public function workCenterDowntimes()
    {
        return $this->hasMany(WorkCenterDowntime::class, 'work_center_id', 'id');
    }

    //belongsToMany
    public function downtimes()
    {
        return $this->belongsToMany(Downtime::class, WorkCenterDowntime::TABLE_NAME, 'work_center_id', 'downtime_id', 'id', 'id')->withPivot(['opc_tag_id', 'state', 'value_updated_at']);
    }

    //belongsToMany
    public function downtimeOpcTags()
    {
        return $this->belongsToMany(OpcTag::class, WorkCenterDowntime::TABLE_NAME, 'work_center_id', 'opc_tag_id', 'id', 'id')->withPivot(['downtime_id', 'state', 'value_updated_at']);
    }

    //belongsTo break_schedule
    public function breakSchedule()
    {
        return $this->belongsTo(BreakSchedule::class, 'break_schedule_id', 'id');
    }

    //belongsToMany Users
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_work_center', 'work_center_id', 'user_id', 'id', 'id')->withPivot(['terminal_permission']);
    }

    public function monitorClients()
    {
        return $this->hasMany(MonitorClient::class, 'target_id', 'id');
    }
}
