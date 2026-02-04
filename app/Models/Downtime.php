<?php

namespace App\Models;

use App\Events\Opc\DowntimeStateChangedEvent;
use App\Events\Terminal\WorkCenterDowntimeStateChangeEvent;
use App\Extras\Support\ModelDestroyable;
use App\Jobs\HourlyProductionUpdate;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned integer
 * 
 * @property int $plant_id Foreign Key (Plant): unsigned integer
 * @property int $downtime_type_id Foreign Key (DowntimeType): unsigned integer
 * 
 * @property string $category string
 * @property int $enabled tinyinteger (0: Disabled, 1: Enabled)
 * 
 * @property string $created_at timestamp
 * @property string $updated_at timestamp
 */
class Downtime extends Model implements ModelDestroyable
{
    const TABLE_NAME = 'downtimes';
    protected $table = self::TABLE_NAME;

    //relationships
    public function updateStateValue($workCenterId, $state, $updated_at)
    {
        $newState = $state ? 1 : 0;

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $this->workCenters()->wherePivot('work_center_id', '=', $workCenterId)->first();
        if (!$workCenter)
            return;

        //WorkCenterDowntime State
        $prevState = $workCenter->pivot->state;

        $workCenter->pivot->state = $newState;
        $workCenter->pivot->value_updated_at = $updated_at;
        $workCenter->pivot->save();

        // State Log
        $log = new DowntimeStateLog();
        $log->state = $newState;
        $log->recorded_at = $updated_at;
        $log->work_center_id = $workCenter->id;
        $log->opc_tag_id = $workCenter->pivot->opc_tag_id;


        $log->downtime_id = $this->id;

        $log->setConnection($this->connection);
        $log->save();

        $shouldTriggerDataUpdatedEvent = false;

        /** @var \App\Models\Production $currentProduction */
        $currentProduction = $workCenter->currentProduction;

        if ($currentProduction) {

            // Production Event

            if ($newState) { //Downtime Started

                /** @var \App\Models\DowntimeEvent $downtimeEvent */
                $downtimeEvent = DowntimeEvent::on($this->connection)
                    ->where('production_id', '=', $currentProduction->id)
                    ->where('downtime_id', '=', $this->id)
                    ->whereNull('end_time')
                    ->where('start_time', '<=', $updated_at)
                    ->orderBy('start_time', 'desc')
                    ->first();

                if (!$downtimeEvent) { //no duplicate event, create new

                    /** @var \App\Models\DowntimeEvent $downtimeEvent */
                    $downtimeEvent = new DowntimeEvent();
                    if ($this->downtime_type_id == 1) //hardcoded downtime type
                        $downtimeEvent->event_type =  WorkCenter::DOWNTIME_STATUS_UNPLAN_MACHINE;
                    else
                        $downtimeEvent->event_type =  WorkCenter::DOWNTIME_STATUS_UNPLAN_HUMAN;

                    $downtimeEvent->production_id = $currentProduction->id;
                    $downtimeEvent->downtime_id = $this->id;
                    $downtimeEvent->start_time = $updated_at;

                    $downtimeEvent->setConnection($this->connection);
                    $downtimeEvent->save();
                    $shouldTriggerDataUpdatedEvent = true;
                }
            } else { //Downtime Ended


                /** @var \App\Models\DowntimeEvent $downtimeEvent */
                $downtimeEvents = DowntimeEvent::on($this->connection)
                    ->where('production_id', '=', $currentProduction->id)
                    ->where('downtime_id', '=', $this->id)
                    ->whereNull('end_time')
                    ->where('start_time', '<=', $updated_at)
                    ->orderBy('start_time', 'desc')
                    ->get();

                foreach($downtimeEvents as $downtimeEvent) {

                    $downtimeEvent->end_time = $updated_at;
                    $downtimeEvent->save();
                    $shouldTriggerDataUpdatedEvent = true;
                }
            }
            dispatch(new HourlyProductionUpdate($workCenter->plant, $currentProduction, false));
            //$currentProduction->updateHourlySummary()->save();
            $workCenter->updateDowntimeState($shouldTriggerDataUpdatedEvent)->save();
        }

        if ($prevState != $newState) {
            try {
                event(new WorkCenterDowntimeStateChangeEvent($workCenter));
            } catch (\Exception $ex) {
            }
        }

        // try {
        //     event(new DowntimeStateChangedEvent($workCenter, $log));
        // } catch (Exception $ex) {
        // }
    }

    public function isDestroyable(string &$reason = null): bool
    {
        //TODO, only return true when no other resource references to this
        if (!$this->downtimeEvents()->first())
            return true;

        if (!$this->downtimeReasons()->first())
            return true;

        if (!$this->workCenterDowntimes()->first())
            return true;

        return false;
    }

    //hasmany downtime_state_logs
    public function downtimeStateLogs()
    {
        return $this->hasMany(DowntimeStateLog::class, 'downtime_id', 'id');
    }

    //belongto plant_id
    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plant_id', 'id');
    }

    //belongto downtime_type_id
    public function downtimeType()
    {
        return $this->belongsTo(DowntimeType::class, 'downtime_type_id', 'id');
    }

    //hasmany downtime_events
    public function downtimeEvents()
    {
        return $this->hasMany(DowntimeEvent::class, 'downtime_id', 'id');
    }

    //hasMany downtime_reason
    public function downtimeReasons()
    {
        return $this->hasMany(DowntimeReason::class, 'downtime_id', 'id');
    }

    public function workCenterDowntimes()
    {
        return $this->hasMany(WorkCenterDowntime::class, 'downtime_id', 'id');
    }

    //belongsToMany
    public function workCenters()
    {
        return $this->belongsToMany(WorkCenter::class, WorkCenterDowntime::TABLE_NAME, 'downtime_id', 'work_center_id', 'id', 'id')->withPivot(['opc_tag_id', 'state', 'value_updated_at']);
    }
}
