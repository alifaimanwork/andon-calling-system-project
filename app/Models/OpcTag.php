<?php

namespace App\Models;

use App\Events\Opc\CountUpEvent;
use App\Events\Opc\DowntimeStateChangedEvent;
use App\Jobs\HourlyProductionUpdate;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned integer
 
 * @property int $plant_id Foreign Key (Plant): unsigned integer
 * @property int $work_center_id Foreign Key (WorkCenter): unsigned integer
 * @property int $opc_tag_type_id Foreign Key (OpcTagType): unsigned integer 
 * @property int $opc_server_id Foreign Key (OpcServer): unsigned integer 
 * 
 * @property string $info string
 * @property string $tag string
 * @property string $data_type string
 * @property string $value string
 * @property string $prev_value string
 * 
 * @property string $value_updated_at timestamp
 * @property string $created_at timestamp
 * @property string $updated_at timestamp
 */
class OpcTag extends Model
{
    const TABLE_NAME = 'opc_tags';
    protected $table = self::TABLE_NAME;


    /**
     * @return int value needed to write to opc server (write mode)
     * @return null value not need to write to opc server (read mode)
     */
    public function getTargetValue(): ?int
    {
        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $this->workCenter;
        if (!$workCenter)
            return null;
    }

    public function syncFromActiveTag()
    {

        /** @var \App\Models\OpcActiveTag $activeTag */
        $activeTag = OpcActiveTag::where('tag', '=', $this->tag)->first();
        if (!$activeTag)
            return false;

        $this->value_updated_at = $activeTag->value_updated_at;
        $this->value = $activeTag->value;
        $this->save();

        return true;
    }

    public function updateValue($newValue, $valueUpdatedAt)
    {

        if (!$valueUpdatedAt || $valueUpdatedAt < $this->value_updated_at)
            return;

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $this->workCenter;

        if (!$workCenter)
            return;

        if ($this->opc_tag_type_id == 5) //tag is downtime tag, also check with workcenterDowntime
        {

            /** @var \App\Models\Production $currentProduction */
            $currentProduction = $workCenter->currentProduction;

            /** @var \App\Models\WorkCenterDowntime $workCenterDowntime */
            $workCenterDowntime = $workCenter->workCenterDowntimes()->where(WorkCenterDowntime::TABLE_NAME . '.opc_tag_id', $this->id)->first();

            $downtime = $workCenterDowntime->downtime ?? null;

            $newDowntimeState = $newValue ? 1 : 0;

            if (!$workCenterDowntime || !$currentProduction || !$downtime) {                
                $this->prev_value = $this->value;
                $this->value = $newValue;
                $this->value_updated_at = $valueUpdatedAt;
                $this->save();

                if ($workCenterDowntime) {
                    $workCenterDowntime->state = $newDowntimeState;
                    $workCenterDowntime->value_updated_at = $valueUpdatedAt;
                    $workCenterDowntime->save();
                }

                return;
            }

            $currentProduction->triggerDowntime($newDowntimeState, $valueUpdatedAt, $workCenterDowntime, $downtime);

            dispatch(new HourlyProductionUpdate($workCenter->plant, $currentProduction, false));
            //$currentProduction->updateHourlySummary()->save();

            $workCenter->updateDowntimeState(true)->save();
        } elseif ($this->opc_tag_type_id == 4) {



            $currentProduction = $workCenter->currentProduction;

            $lineNo = $this->info;
            $productionLine = null;


            if ($currentProduction) {
                /** @var \App\Models\ProductionLine $productionLine */
                $productionLine = $currentProduction->productionLines()->where('line_no', '=', $lineNo)->first();
            }

            $this->prev_value = $this->value;
            $this->value = $newValue;
            $this->value_updated_at = $valueUpdatedAt;
            $this->save();

            if (!$currentProduction || !$productionLine) {
                return;
            }

            $counterLog = new CounterLog();
            $counterLog->work_center_id = $workCenter->id;
            $counterLog->opc_tag_id = $this->id;
            $counterLog->production_line_id = $productionLine->id;
            $counterLog->line_no = $productionLine->line_no;
            $counterLog->tag_value = $this->value;
            $counterLog->work_center_status = $workCenter->status;
            $counterLog->recorded_at = $valueUpdatedAt;
            $counterLog->setConnection($this->connection)->save();

            // DB::connection($this->connection)
            //     ->transaction(function () use ($newValue, $valueUpdatedAt, $workCenter, $productionLine) {
            //         $opcTag = DB::connection($this->connection)->table(OpcTag::TABLE_NAME)->sharedLock()->find($this->id);

            //         if (!$opcTag)
            //             return true;

            //         $delta = $newValue - $opcTag->value;


            //         DB::connection($this->connection)->update(
            //             'UPDATE `' . OpcTag::TABLE_NAME . '` ' .
            //                 'SET `prev_value`= ?, `value`= ?, `value_updated_at` = ? ' .
            //                 'WHERE `id` = ?',
            //             [$opcTag->value, $newValue, $valueUpdatedAt, $this->id]
            //         ); //opc tag


            //         if ($delta <= 0)
            //             return true;

            //         DB::connection($this->connection)->insert(
            //             'INSERT INTO `' . CounterLog::TABLE_NAME . '` ' .
            //                 '(`work_center_id`,`opc_tag_id`,`production_line_id`,`line_no`,`recorded_at`,`count`) VALUES (' .
            //                 '?,?,?,?,?,?)',
            //             [
            //                 $workCenter->id, $this->id, $productionLine->id, $productionLine->line_no,  $valueUpdatedAt, $delta
            //             ]
            //         ); //create count log

            //     });
            $productionLine->queueProductionLineCountUpdate($workCenter)->save();
            // $this->generateCountUpEvent();

        } else {
            $this->prev_value = $this->value;
            $this->value = $newValue;
            $this->value_updated_at = $valueUpdatedAt;
            $this->save();
        }
    }

    public function generateCountUpEvent()
    {
        $productionLine = null;
        $lineNo = $this->info;


        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $this->workCenter;

        if (!$workCenter)
            return;

        /** @var \App\Models\Production $currentProduction */
        $currentProduction = $workCenter->currentProduction;

        if (!$currentProduction)
            return;

        if ($workCenter->status < WorkCenter::STATUS_FIRST_CONFIRMATION)
            return; //only countup when in first product confirmation & production running

        /** @var \App\Models\ProductionLine $productionLine */
        $productionLine = $currentProduction->productionLines()->where('line_no', '=', $lineNo)->first();

        if (!$productionLine)
            return;

        $counterLog = new CounterLog();
        $counterLog->work_center_id = $workCenter->id;
        $counterLog->opc_tag_id = $this->id;
        $counterLog->production_line_id = $productionLine->id ?? null;
        $counterLog->line_no = $lineNo ?? null;
        $counterLog->count = $this->getCountUpDelta();
        $counterLog->recorded_at = $this->value_updated_at;


        $counterLog->setConnection($this->getConnectionName());
        $counterLog->save();

        $productionLine->queueProductionLineCountUpdate($workCenter)->save();

        //$productionLine->updateActualOutput()->updateOkCount()->queueProductionLineCountUpdate($workCenter)->save();
    }


    public function getCountUpDelta()
    {
        if (is_null($this->prev_value))
            return $this->value;

        $delta = $this->value - $this->prev_value;
        if ($delta < 0)
            return $this->value;

        return $delta;
    }
    public function activateTag()
    {
        //check 

        //add entry
        $opcActiveTag = OpcActiveTag::where('opc_server_id', '=', $this->opc_server_id)->where('tag', '=', $this->tag)->first();
        if ($opcActiveTag && (!$opcActiveTag->enabled || $opcActiveTag->plant_id != $this->plant_id)) {
            $opcActiveTag->plant_id = $this->plant_id;
            $opcActiveTag->enabled = 1;
            $opcActiveTag->save();
            return;
        }

        $opcActiveTag = new OpcActiveTag();
        $opcActiveTag->plant_id = $this->plant_id;
        $opcActiveTag->opc_server_id = $this->opc_server_id;
        $opcActiveTag->tag = $this->tag;
        $opcActiveTag->save();
    }

    //relationships

    //hasmany counter_logs
    public function counterLogs()
    {
        return $this->hasMany(CounterLog::class, 'opc_tag_id', 'id');
    }

    //hasmany downtime_state_logs
    public function downtimeStateLogs()
    {
        return $this->hasMany(DowntimeStateLog::class, 'opc_tag_id', 'id');
    }

    //belongto work_center_id
    public function workCenter()
    {
        return $this->belongsTo(WorkCenter::class, 'work_center_id', 'id');
    }

    //belongto opc_tag_type_id
    public function opcTagType()
    {
        return $this->belongsTo(OpcTagType::class, 'opc_tag_type_id', 'id');
    }

    //belongto downtime_id
    public function downtime()
    {
        return $this->belongsToMany(Downtime::class, WorkCenterDowntime::TABLE_NAME, 'opc_tag_id', 'downtime_id', 'id', 'id');
    }

    //belongto opc_Server_id
    public function opcServer()
    {
        return $this->belongsTo(OpcServer::class, 'opc_server_id', 'id');
    }

    public function workCenterDowntimes()
    {
        return $this->hasMany(WorkCenterDowntime::class, 'opc_tag_id', 'id');
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plant_id', 'id');
    }
}
