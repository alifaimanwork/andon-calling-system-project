<?php

namespace App\Models;

use App\Extras\Casts\AsNullableArrayObject;
use App\Extras\Payloads\PendingData;
use App\Extras\Payloads\RejectData;
use App\Extras\Payloads\ReworkData;
use App\Extras\Payloads\GenericRequestResult;
use App\Extras\Utils\ProductionCalculator;
use App\Jobs\ProductionLineCountUpdate;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned long integer
 * 
 * @property int $production_order_id Foreign Key (productionOrder): unsigned long integer
 * @property int $production_id Foreign Key (production): unsigned long integer
 * 
 * @property int $line_no unsigned long integer
 * 
 * @property array $part_data text (cast to arrayobject) snapshot of part data at production
 * 
 * @property int $plan_quantity unsigned integer balance plan quantity
 * @property int $actual_output unsigned integer
 * @property int $reject_count unsigned integer
 * @property int $ok_count unsigned integer
 * @property float $reject_percentage float reject_count / actual_output
 * 
 * @property int $pending_count unsigned integer
 * @property int $pending_ok unsigned integer
 * @property int $pending_ng unsigned integer
 * @property int $rework_status tiny Integer 0: open, 1: completed
 * 
 * @property array $reject_summary text ArrayObject
 * @property array $hourly_summary text ArrayObject
 * @property array $overall_summary text ArrayObject
 * 
 * @property float $oee OEE
 * @property float $availability availability
 * @property float $performance performance
 * @property float $quality quality

 * 
 * 
 * @property string $created_at timestamp
 * @property string $updated_at timestamp
 * 
 * 
 * Append Attributes
 * 
 * @property int $standard_output int
 */
class ProductionLine extends Model
{
    /** Rework Status Open */
    const REWORK_STATUS_OPEN = 0;
    /** Work Center Die Change */
    const REWORK_STATUS_COMPLETED = 1;

    const TABLE_NAME = 'production_lines';
    protected $table = self::TABLE_NAME;

    protected $guarded = [];


    protected $casts = [
        'part_data' => AsArrayObject::class,
        'reject_summary' => AsNullableArrayObject::class,
        'hourly_summary' => AsNullableArrayObject::class,
        'overall_summary' => AsNullableArrayObject::class
    ];

    protected $appends = ['reject_percentage'];

    public function getRejectPercentageAttribute()
    {
        if ($this->actual_output <= 0)
            return 0;

        return $this->reject_count / $this->actual_output;
    }
    public function snapshotPartData()
    {
        /** @var \App\Models\Part $part */
        $part = $this->part;

        //cache size optimization
        unset($part->updated_at);
        unset($part->created_at);

        if ($part)
            $part->partRejectTypes; //touch partRejectTypes to load
        foreach ($part->partRejectTypes as &$partRejectType) {
            unset($partRejectType->updated_at);
            unset($partRejectType->created_at);
            unset($partRejectType);
        }
        $this->part_data = $part;

        return $this;
    }
    public function recalculateTagCount()
    {
        $counterLogs = DB::connection($this->getConnectionName())
            ->table(CounterLog::TABLE_NAME)
            ->select(['id', 'count', 'tag_value', 'work_center_status', 'recorded_at'])
            ->where('production_line_id', '=', $this->id)
            ->whereNotNull('tag_value')
            ->orderBy('recorded_at', 'asc')
            ->get();

        if (count($counterLogs) <= 0)
            return $this;

        $prevValue = $counterLogs[0]->tag_value;
        $lastRecordedAt = $counterLogs[0]->recorded_at;
        foreach ($counterLogs as $counterLog) {
            if ($lastRecordedAt == $counterLog->recorded_at)
                continue;

            if ($counterLog->work_center_status == WorkCenter::STATUS_RUNNING || $counterLog->work_center_status == WorkCenter::STATUS_FIRST_CONFIRMATION) {
                $count = $counterLog->tag_value - $prevValue;
                if ($count < 0)
                    $count = 0;
            } else
                $count = 0;



            if ($counterLog->count != $count) {
                DB::connection($this->getConnectionName())->update('UPDATE ' . CounterLog::TABLE_NAME . ' SET `count`= ? WHERE `id` = ?', [$count, $counterLog->id]);
            }
            $prevValue = $counterLog->tag_value;
        }


        return $this;
    }
    public function updateActualOutput()
    {
        $result = DB::connection($this->getConnectionName())
            ->table(CounterLog::TABLE_NAME)
            ->select([DB::raw("CONVERT(SUM(`" . CounterLog::TABLE_NAME . "`.`count`),UNSIGNED) AS total")])
            ->where('production_line_id', '=', $this->id)
            ->first();

        $this->actual_output = ($result->total ?? 0);

        return $this;
    }
    public function getTotalRejectCount(array $rejectGroupIds)
    {
        $total = 0;
        if (!$this->reject_summary)
            $this->updateRejectCount();

        foreach ($this->reject_summary as $rejectGroupId => $rejects) {
            if (!in_array($rejectGroupId, $rejectGroupIds))
                continue;

            $total += $rejects['total'];
        }

        return $total;
    }
    public function updateRejectCount()
    {
        $result = DB::connection($this->getConnectionName())
            ->table(Reject::TABLE_NAME)
            ->select([Reject::TABLE_NAME . '.reject_type_id', RejectType::TABLE_NAME . '.reject_group_id', DB::raw("CONVERT(SUM(`" . Reject::TABLE_NAME . "`.`count`),UNSIGNED) AS total")])
            ->join(RejectType::TABLE_NAME, Reject::TABLE_NAME . '.reject_type_id', '=', RejectType::TABLE_NAME . '.id')
            ->join(RejectGroup::TABLE_NAME, RejectType::TABLE_NAME . '.reject_group_id', '=', RejectGroup::TABLE_NAME . '.id')
            ->where('production_line_id', '=', $this->id)
            ->groupBy('reject_type_id', 'reject_group_id')
            ->get();

        //summarize
        $this->reject_summary = [];

        $totalReject = 0;
        foreach ($result as $row) {
            $totalReject += $row->total;

            if (!isset($this->reject_summary[$row->reject_group_id])) {
                $this->reject_summary[$row->reject_group_id] = [];
                $this->reject_summary[$row->reject_group_id]['total'] = 0;
            }
            if (!isset($this->reject_summary[$row->reject_group_id][$row->reject_type_id]))
                $this->reject_summary[$row->reject_group_id][$row->reject_type_id] = 0;

            $this->reject_summary[$row->reject_group_id][$row->reject_type_id] += $row->total;
            $this->reject_summary[$row->reject_group_id]['total'] += $row->total;
        }

        $this->reject_count = ($totalReject ?? 0);
        $this->updateOkCount();
        return $this;
    }
    public function updateOkCount()
    {
        $this->ok_count = $this->actual_output - $this->reject_count;
        return $this;
    }

    public function updatePendingCount()
    {
        $result = DB::connection($this->getConnectionName())
            ->table(Pending::TABLE_NAME)
            ->select([DB::raw("CONVERT(SUM(`" . Pending::TABLE_NAME . "`.`count`),UNSIGNED) AS total")])
            ->where('production_line_id', '=', $this->id)
            ->first();

        $this->pending_count = ($result->total ?? 0);

        return $this;
    }
    public function updateReworkCount()
    {
        $result = DB::connection($this->getConnectionName())
            ->table(Rework::TABLE_NAME)
            ->select([
                DB::raw("CONVERT(SUM(`" . Rework::TABLE_NAME . "`.`ok_count`),UNSIGNED) AS pending_ok"),
                DB::raw("CONVERT(SUM(`" . Rework::TABLE_NAME . "`.`ng_count`),UNSIGNED) AS pending_ng")
            ])
            ->where('production_line_id', '=', $this->id)
            ->first();


        $this->pending_ok = ($result->pending_ok ?? 0);
        $this->pending_ng = ($result->pending_ng ?? 0);

        return $this;
    }
    public function updateOverallSummary(Production $production = null) //pass production to prevent from recalc runtime summary
    {
        if (!$production || $production->id != $this->production_id)
            $production = $this->production;

        $overallBlock = [
            //Similar to Live Data @ LiveProduction.js
            "actual_output" => $this->actual_output,
            "availability" => 0,
            "oee" => 0,
            "ok_count" => $this->ok_count,
            "pending_count" => $this->pending_count,
            "performance" => 0,
            "plan_quantity" => $this->plan_quantity,
            "plan_variance" => 0,
            "quality" => 0,
            "reject_count" => $this->reject_count,
            "standard_output" => 0,
            "variance" => 0,
        ];
        $runtimeSummary = $production->runtime_summary;
        $balanceTime = 0;
        $overallCalculated = ProductionCalculator::calculateOee($this, ['runtime_summary' => $runtimeSummary], $overallBlock, $balanceTime);

        //update values to block
        foreach ($overallCalculated as $key => $value) {
            $overallBlock[$key] = $value;
        }

        $this->overall_summary = $overallBlock;
        $this->updateOee();

        return $this;
    }

    public function updateOee()
    {
        $this->oee = $this->overall_summary['oee'] ?? 0;
        $this->availability = $this->overall_summary['availability'] ?? 0;
        $this->performance = $this->overall_summary['performance'] ?? 0;
        $this->quality = $this->overall_summary['quality'] ?? 0;
        $this->standard_output = $this->overall_summary['standard_output'] ?? 0;

        return $this;
    }
    public function queueProductionLineCountUpdate(WorkCenter $workCenter = null)
    {
        if (!$workCenter) {
            /** @var \App\Models\Production $production */
            $production = $this->production;
            if (!$production)
                return $this;

            /** @var \App\Models\WorkCenter $workCenter */
            $workCenter = $production->workCenter;
            if (!$workCenter)
                return $this;
        }

        /** @var \App\Models\Plant $plant */
        $plant = $workCenter->plant;
        if (!$plant)
            return $this;

        dispatch(new ProductionLineCountUpdate($plant, $workCenter, $this));

        return $this;
    }
    public function updateHourlySummary($productionHourlySummary = null)
    {
        // DO FULL UPDATE if (!$this->hourly_summary)
        $this->hourly_summary = [];

        /** @var \App\Models\Production $production */
        $production = $this->production;

        if (!$production || !$production->started_at)
            return $this;


        if (!$productionHourlySummary)
            $productionHourlySummary = $production->hourly_summary;

        if (!$productionHourlySummary)
            return $this;


        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $production->workCenter;

        if (!$workCenter)
            return $this;


        /** @var \App\Models\Plant $plant */
        $plant = $workCenter->plant;

        if (!$plant)
            return $this;

        $localTimeZone = $plant->getLocalDateTimeZone();
        $utcTimeZone = new \DateTimeZone('UTC');

        //pre allocate blocks
        $now = new \DateTime();
        $current = clone $production->started_at;
        $end = $production->stopped_at ?? $now;
        if ($end > $now)
            $end = $now;

        //quick check duration
        $duration = $end->getTimestamp() - $current->getTimestamp();
        if ($duration < 0 || $duration > 86400) //Invalid start & end
            return $this;

        $hourInterval = new \DateInterval('PT1H');


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
                    'start' => $current->format('c'),
                    'end' => $endBlock->format('c'),

                    //Similar to Live Data @ LiveProduction.js
                    "actual_output" => 0, //OK
                    "actual_output_accumulate" => 0,

                    "availability" => 0,
                    "oee" => 0,
                    "ok_count" => 0, //OK
                    "pending_count" => 0, //OK
                    "performance" => 0,
                    "plan_quantity" => $this->plan_quantity,
                    "plan_variance" => 0,
                    "quality" => 0,
                    "reject_count" => 0, //OK
                    "standard_output" => 0,
                    "standard_output_accumulate" => 0,

                    "variance" => 0,

                    //Reject Summary (Similar to reject summary properties)
                    /*
                     <reject_group_id> => [<reject_type_id>:<count>, ... ,"total":<total_count>]
                    */
                    'reject_summary' => [ //OK
                        '1' => ['total' => 0], //setting
                        '2' => ['total' => 0], //process
                        '3' => ['total' => 0], //material
                    ],

                    'reject_summary_accumulate' => [ //OK
                        '1' => ['total' => 0], //setting
                        '2' => ['total' => 0], //process
                        '3' => ['total' => 0], //material
                    ],

                ];
            }
            $current = $endBlock;
        }

        //TODO: optimized update, currently recount all

        //reset count
        foreach ($this->hourly_summary as $label => &$block) {
            $block['actual_output'] = 0;
            $block['reject_count'] = 0;
            $block['reject_summary'] = [
                '1' => ['total' => 0], //setting
                '2' => ['total' => 0], //process
                '3' => ['total' => 0], //material
            ];
            unset($block);
        }

        //update actual count
        $counterLogs = DB::connection($this->connection)
            ->table(CounterLog::TABLE_NAME)
            ->select(['count', 'recorded_at'])
            ->where('production_line_id', '=', $this->id)
            ->get();


        foreach ($counterLogs as $counterLog) {
            $recordTime = new \DateTime($counterLog->recorded_at);
            $recordTime->setTimezone($localTimeZone);
            $blockLabel = $recordTime->format('H');
            //get block
            if (!isset($this->hourly_summary[$blockLabel]))
                continue;

            $block = &$this->hourly_summary[$blockLabel];
            $block['actual_output'] += $counterLog->count;
        }


        //update reject count

        $rejects = DB::connection($this->connection)
            ->table(Reject::TABLE_NAME)
            ->select(['reject_group_id', 'reject_type_id', 'count', 'recorded_at'])
            ->join(RejectType::TABLE_NAME, Reject::TABLE_NAME . '.reject_type_id', '=', RejectType::TABLE_NAME . '.id')
            ->where('production_line_id', '=', $this->id)
            ->get();

        foreach ($rejects as $reject) {

            $recordTime = new \DateTime($reject->recorded_at);
            $recordTime->setTimezone($localTimeZone);
            $blockLabel = $recordTime->format('H');
            //get block
            if (!isset($this->hourly_summary[$blockLabel]))
                continue;

            $block = &$this->hourly_summary[$blockLabel];

            $block['reject_count'] += $reject->count;

            if (!isset($block['reject_summary'][strval($reject->reject_group_id)]))
                continue;

            $rejectBlock = &$block['reject_summary'][strval($reject->reject_group_id)];
            $rejectBlock['total'] += $reject->count;

            if (!isset($rejectBlock[strval($reject->reject_type_id)]))
                $rejectBlock[strval($reject->reject_type_id)] = 0;

            $rejectBlock[strval($reject->reject_type_id)] += $reject->count;
        }


        //update pending count

        $pendings = DB::connection($this->connection)
            ->table(Pending::TABLE_NAME)
            ->select(['count', 'recorded_at'])
            ->where('production_line_id', '=', $this->id)
            ->get();

        foreach ($pendings as $pending) {

            $recordTime = new \DateTime($pending->recorded_at);
            $recordTime->setTimezone($localTimeZone);
            $blockLabel = $recordTime->format('H');
            //get block
            if (!isset($this->hourly_summary[$blockLabel]))
                continue;

            $block = &$this->hourly_summary[$blockLabel];

            $block['pending_count'] += $pending->count;
        }



        //update block summary
        $blocks = [];
        $balanceTime = 0;
        foreach ($this->hourly_summary as $key => &$block) {

            //find runtime block
            $blocks[] = &$block;
            if (!isset($productionHourlySummary[$key]))
                continue;

            $runtimeBlock = $productionHourlySummary[$key];
            if (!$runtimeBlock)
                continue;


            if (!is_array($runtimeBlock))
                $runtimeBlock = $runtimeBlock->toArray();

            if (!is_array($runtimeBlock['runtime_summary']))
                $runtimeBlock['runtime_summary'] = (array)$runtimeBlock['runtime_summary'];

            $calculated = ProductionCalculator::calculateOee($this, $runtimeBlock, $block, $balanceTime);

            //update values to block
            foreach ($calculated as $key => $value) {
                $block[$key] = $value;
            }
            unset($block);
        }

        //Update accumulate Block
        //valid only block is sorted
        usort($blocks, function ($a, $b) {
            return $a['start'] <=> $b['start'];
        });

        $actualOutputAccumulate = 0;
        $standardOutputAccumulate = 0;
        $rejectSummaryAccumulated = [ //OK
            '1' => ['total' => 0], //setting
            '2' => ['total' => 0], //process
            '3' => ['total' => 0], //material
        ];

        foreach ($blocks as &$block) {

            //add current block to accumulated
            $actualOutputAccumulate += $block['actual_output'];
            $standardOutputAccumulate += $block['standard_output'];
            foreach ($block['reject_summary'] as $rejectTypeId => $rejectSummary) {
                foreach ($rejectSummary as $rejectId => $rejectItem) {
                    if (!isset($rejectSummaryAccumulated[$rejectTypeId][$rejectId]))
                        $rejectSummaryAccumulated[$rejectTypeId][$rejectId] = 0;
                    $rejectSummaryAccumulated[$rejectTypeId][$rejectId] += $rejectItem;
                }
            }

            //store accumulated to current block accumulated
            $block['actual_output_accumulate'] = $actualOutputAccumulate;
            $block['standard_output_accumulate'] = $standardOutputAccumulate;

            foreach ($rejectSummaryAccumulated as $rejectTypeId => $rejectSummary) {
                foreach ($rejectSummary as $rejectId => $rejectItem) {
                    if (!isset($block['reject_summary_accumulate'][$rejectTypeId][$rejectId]))
                        $block['reject_summary_accumulate'][$rejectTypeId][$rejectId] = 0;

                    $block['reject_summary_accumulate'][$rejectTypeId][$rejectId] = $rejectItem;
                }
            }
            unset($block);
        }

        return $this;
    }

    public function setReject(array $rejectsData, WorkCenter $workCenter = null, Production $production = null): GenericRequestResult
    {

        if (!$this->part_data['part_reject_types'])
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid Reject Type");


        //reject type check
        /** @var \App\Extras\Payloads\RejectData $rejectData */
        foreach ($rejectsData as $rejectData) {
            $partRejectType = null;


            if (is_array($this->part_data['part_reject_types'])) {

                foreach ($this->part_data['part_reject_types'] as $rejectType) //Use data from snapshot
                {

                    if ($rejectData->reject_type_id == $rejectType['id']) {
                        /** @var \App\Models\RejectType $partRejectType */
                        $partRejectType = $rejectType;
                    }
                }
            }

            if (!$partRejectType)
                return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid Reject Type");
        }



        //Log Reject

        $now = date('Y-m-d H:i:s');
        /** @var \App\Extras\Payloads\RejectData $rejectData */
        foreach ($rejectsData as $rejectData) {
            $reject = new Reject();
            $reject->production_line_id = $rejectData->production_line_id;
            $reject->reject_type_id = $rejectData->reject_type_id;
            $reject->count = $rejectData->count;
            $reject->recorded_at = $now;
            $reject->setConnection($this->connection)->save();
        }

        //Recalculate reject
        $this->updateRejectCount()->updateHourlySummary()->updateOverallSummary()->save();

        //Trigger
        if (!$production) {
            /** @var \App\Models\Production|null $production */
            $production = $this->production;
        }

        if (!$production) //skip broadcast
            return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK");

        if (!$workCenter) {
            /** @var \App\Models\WorkCenter|null $workCenter */
            $workCenter = $production->workCenter;
        }

        if (!$workCenter) //skip broadcast
            return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK");

        if ($workCenter->current_production_id == $production->id)
            $workCenter->broadcastWorkCenterDataUpdate();

        return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK");
    }

    public function setPending(PendingData $pendingData, WorkCenter $workCenter = null, Production $production = null): GenericRequestResult
    {
        //Log Pending
        if ($pendingData->production_line_id != $this->id)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid ID");

        $now = date('Y-m-d H:i:s');

        $pending = new Pending();
        $pending->production_line_id = $pendingData->production_line_id;
        $pending->count = $pendingData->count;
        $pending->recorded_at = $now;
        $pending->setConnection($this->connection)->save();


        //Recalculate reject
        $this->updatePendingCount()->updateHourlySummary()->updateOverallSummary()->save();

        //Trigger
        if (!$production) {
            /** @var \App\Models\Production|null $production */
            $production = $this->production;
        }

        if (!$production) //skip broadcast
            return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK");

        if (!$workCenter) {
            /** @var \App\Models\WorkCenter|null $workCenter */
            $workCenter = $production->workCenter;
        }

        if (!$workCenter) //skip broadcast
            return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK");

        if ($workCenter->current_production_id == $production->id)
            $workCenter->broadcastWorkCenterDataUpdate();

        return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK");
    }

    public function setRework(ReworkData $reworkData, WorkCenter $workCenter = null): GenericRequestResult
    {
        //Log Pending
        if ($reworkData->production_line_id != $this->id)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid ID");

        $now = date('Y-m-d H:i:s');

        $user = User::getCurrent();

        $rework = new Rework();
        $rework->production_line_id = $reworkData->production_line_id;
        $rework->user_id = $user->id ?? null;
        $rework->ok_count = $reworkData->ok_count;
        $rework->ng_count = $reworkData->ng_count;
        $rework->recorded_at = $now;
        $rework->setConnection($this->connection)->save();

        //Recalculate rework
        $this->updateReworkCount()->save();

        //SAP Export
        if ($reworkData->ok_count > 0)
            $this->exportRWOK($user, $rework->ok_count);
        if ($reworkData->ng_count > 0)
            $this->exportRWNG($user, $rework->ng_count);

        //Trigger
        if (!$workCenter) {
            /** @var \App\Models\WorkCenter|null $workCenter */
            $workCenter = $this->workCenter;
        }

        if (!$workCenter) //skip broadcast
            return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK");

        $workCenter->broadcastWorkCenterReworkUpdate($this);

        return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK");
    }

    public function closeRework(WorkCenter $workCenter = null): GenericRequestResult
    {

        // $now = date('Y-m-d H:i:s');

        // $user = User::getCurrent();


        if ($this->rework_status == ProductionLine::REWORK_STATUS_COMPLETED)
            return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK");


        //TODO: log activity

        $this->rework_status = ProductionLine::REWORK_STATUS_COMPLETED;
        $this->save();

        if (!$workCenter) {
            /** @var \App\Models\WorkCenter|null $workCenter */
            $workCenter = $this->workCenter;
        }

        if (!$workCenter) //skip broadcast
            return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK");

        $workCenter->broadcastWorkCenterReworkUpdate($this);

        return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK");
    }

    //SAP Exports//
    public function exportGRNG($user)
    {

        $log = new GRNGLog();
        $log->production_line_id = $this->id;
        $log->user_id = $user->id ?? null;
        $log->setConnection($this->connection)
            ->updateData()
            ->save();

        $log->export();
        return $this;
    }
    public function exportGRQI($user)
    {

        $log = new GRQILog();
        $log->production_line_id = $this->id;
        $log->user_id = $user->id ?? null;
        $log->setConnection($this->connection)
            ->updateData()
            ->save();

        $log->export();
        return $this;
    }

    public function exportRWOK($user, $count)
    {
        $log = new RWOKLog();
        $log->production_line_id = $this->id;
        $log->user_id = $user->id ?? null;
        $log->setConnection($this->connection)
            ->updateData($count)
            ->save();

        $log->export();
        return $this;
    }
    public function exportRWNG($user, $count)
    {
        $log = new RWNGLog();
        $log->production_line_id = $this->id;
        $log->user_id = $user->id ?? null;
        $log->setConnection($this->connection)
            ->updateData($count)
            ->save();

        $log->export();
        return $this;
    }
    public function exportPendingGROK($user, $includeIncompletePackaging = false)
    {


        //get last grok
        $lastLog = $this->GROKLogs()->orderBy('batch_no', 'desc')->first();

        if ($lastLog) {
            $lastBatchNo = $lastLog->batch_no;
        } else {
            $lastBatchNo = 0;
        }

        $standardPackaging = $this->part_data['packaging'];
        $nextCount = 0;
        if ($standardPackaging > 0)
            $nextCount = ($lastBatchNo + 1) * $standardPackaging;
        elseif (!$includeIncompletePackaging)
            return $this; //invalid standard packaging, abort;

        if ($standardPackaging < 0 && $includeIncompletePackaging) {
            //invalid standard packaging, but force export,
            $lastBatchNo++;
            $log = new GROKLog();
            $log->production_line_id = $this->id;
            $log->user_id = $user->id ?? null;
            $log->batch_no = $lastBatchNo;
            $log->count = $this->ok_count;
            $log->setConnection($this->connection)
                ->updateData($this)
                ->save();

            $log->export();
            return;
        }

        if (!$standardPackaging)
            return;

        Log::info("exportPendingGROK, " . $lastBatchNo . ', ' . $nextCount . ', ' . $this->ok_count);

        while ($nextCount <= $this->ok_count) {
            $lastBatchNo++;
            $log = new GROKLog();
            $log->production_line_id = $this->id;
            $log->user_id = $user->id ?? null;
            $log->batch_no = $lastBatchNo;
            $log->count = $standardPackaging;
            $log->setConnection($this->connection)
                ->updateData($this)
                ->save();

            $log->export();
            $nextCount += $standardPackaging;
        }

        if ($includeIncompletePackaging) {
            $balanceCount = $this->ok_count - ($nextCount - $standardPackaging);
            if ($balanceCount > 0) {
                $lastBatchNo++;
                $log = new GROKLog();
                $log->production_line_id = $this->id;
                $log->user_id = $user->id ?? null;
                $log->batch_no = $lastBatchNo;
                $log->count = $balanceCount;
                $log->setConnection($this->connection)
                    ->updateData($this)
                    ->save();

                $log->export();
            }
        }
        return $this;
    }

    public function exportETTOP10()
    {
        $log = new ETTOP10Log();
        $log->production_line_id = $this->id;
        $log->setConnection($this->connection)
            ->updateData()
            ->save();

        $log->export();
        return $this;
    }

    public function exportETTOP20()
    {
        $log = new ETTOP20Log();
        $log->production_line_id = $this->id;
        $log->setConnection($this->connection)
            ->updateData()
            ->save();

        $log->export();
        return $this;
    }

    //relationships

    //belongto production_orders
    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id', 'id');
    }
    public function WorkCenter()
    {
        return $this->hasOneThrough(WorkCenter::class, Production::class, 'id', 'id', 'production_id', 'work_center_id');
    }


    public function part()
    {
        return $this->hasOneThrough(Part::class, ProductionOrder::class, 'id', 'id', 'production_order_id', 'part_id');
    }
    //belongto production_id
    public function production()
    {
        return $this->belongsTo(Production::class, 'production_id', 'id');
    }

    //hasmany pendings
    public function pendings()
    {
        return $this->hasMany(Pending::class, 'production_line_id', 'id');
    }

    //hasmany reworks
    public function reworks()
    {
        return $this->hasMany(Rework::class, 'production_line_id', 'id');
    }

    //hasmany rejects
    public function rejects()
    {
        return $this->hasMany(Reject::class, 'production_line_id', 'id');
    }

    //hasmany outputs
    public function outputs()
    {
        return $this->hasMany(Output::class, 'production_line_id', 'id');
    }

    //SAP Export Log
    public function GROKLogs()
    {
        return $this->hasMany(GROKLog::class, 'production_line_id', 'id');
    }
    public function GRNGLogs()
    {
        return $this->hasMany(GRNGLog::class, 'production_line_id', 'id');
    }
    public function ETTOP10Logs()
    {
        return $this->hasMany(ETTOP10Log::class, 'production_line_id', 'id');
    }
    public function ETTOP20Logs()
    {
        return $this->hasMany(ETTOP20Log::class, 'production_line_id', 'id');
    }
}
