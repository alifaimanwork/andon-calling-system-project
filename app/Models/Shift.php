<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned integer
 * 
 * @property int $plant_id Foreign Key (Plant): unsigned integer
 * @property int $shift_type_id Foreign Key (ShiftType): unsigned integer
 * 
 * @property int $day_of_week Day of week ISO-8601: 1: Monday ... 7:Sunday
 * @property string $start_time time
 * @property string $normal_duration unsigned integer
 * @property string $duration unsigned integer
 * 
 * @property int $enabled tiny Integer 1: Enabled, 0: Disabled
 * 
 * @property string $updated_at datetime
 * @property string $created_at datetime
 * 
 * @property string $end_time time
 * @property string $over_time time
 */

class Shift extends Model
{
    const TABLE_NAME = 'shifts';
    protected $table = self::TABLE_NAME;

    protected $guarded = [];
    protected $appends = ['end_time', 'over_time'];

    /** @return  bool true: detect overlap, false: not detect */
    public static function disableOverlapShifts(array &$shifts): bool
    {
        ///validate duplicate, day of week

        //disable shift kalau overlap

        // sort by day_of_week
        // usort($shifts, function($a, $b) {
        //     if($a['day_of_week'] == $b['day_of_week']){
        //         //compare with day night
        //         if($a['shift_type'] == 'day' && $b['shift_type'] == 'night'){
        //             return -1;
        //         }
        //         else if($a['shift_type'] == 'night' && $b['shift_type'] == 'day'){
        //             return 1;
        //         }
        //         else{
        //             return 0;
        //         }
        //     }
        //     return $a['day_of_week'] <=> $b['day_of_week'];
        // });

        $shiftsByDay = [];
        for ($i = 1; $i <= 7; $i++) {
            $shiftsByDay[$i] = [];
        }

        foreach ($shifts as &$shift) {
            $shiftsByDay[$shift['day_of_week']][] = $shift;
            unset($shift);
        }

        $overlap = false;
        //Assume no duplicate records
        foreach ($shifts as &$shift) {

            // if (!($shift['enabled'] && Shift::canEnable($shift, $shiftsByDay))) {
            //     $shift['enabled'] = false;
            // }

            if (!($shift['enabled']))
                continue;

            $canEnable = Shift::canEnable($shift, $shiftsByDay);

            if (!$canEnable) {
                $shift['enabled'] = false;
                $overlap = true;
            }
            unset($shift);
        }

        return $overlap;
    }
    public static function canEnable(&$shift, $shiftsByDay)
    {
        //Check day before
        $dayBefore = $shift['day_of_week'] - 1;
        if ($dayBefore) { //if not monday
            foreach ($shiftsByDay[$dayBefore] as $shiftBefore) {
                if ($shiftBefore['enabled'] && Shift::isOverlap($shift, $shiftBefore, true)) {
                    return false;
                }
            }
        }

        //if night, check day
        if ($shift['shift_type'] == 'night') {
            foreach ($shiftsByDay[$shift['day_of_week']] as $shiftCurrentDay) {
                if ($shiftCurrentDay['shift_type'] == 'day')
                    if ($shiftCurrentDay['enabled'] && Shift::isOverlap($shift, $shiftCurrentDay, false)) {
                        return false;
                    }
            }
        }

        return true;
    }

    public static function isOverlap($shift, $shiftBefore, $dayBefore)
    {
        $shiftStartTime = \DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 ' . $shift['start_time'] . ':00');
        $shiftEndTime = \DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 ' . $shift['end_time'] . ':00');
        if ($shift['start_time'] > $shift['end_time'])
            $shiftEndTime = $shiftEndTime->modify('+1 day');

        $shiftBeforeStartTime = \DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 ' . $shiftBefore['start_time'] . ':00');
        if ($dayBefore)
            $shiftBeforeStartTime = $shiftBeforeStartTime->modify('-1 day');
        $shiftBeforeEndTime = \DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 ' . $shiftBefore['end_time'] . ':00');
        if ($dayBefore)
            $shiftBeforeEndTime = $shiftBeforeEndTime->modify('-1 day');

        if ($shiftBefore['start_time'] > $shiftBefore['end_time'])
            $shiftBeforeEndTime = $shiftBeforeEndTime->modify('+1 day');


        if (($shiftStartTime > $shiftBeforeStartTime && $shiftStartTime < $shiftBeforeEndTime) ||
            ($shiftEndTime > $shiftBeforeStartTime && $shiftEndTime < $shiftBeforeEndTime) ||
            ($shiftBeforeStartTime > $shiftStartTime && $shiftBeforeStartTime < $shiftEndTime) ||
            ($shiftBeforeEndTime > $shiftStartTime && $shiftBeforeEndTime < $shiftEndTime) ||
            ($shiftStartTime == $shiftBeforeStartTime && $shiftEndTime == $shiftBeforeEndTime)
        ) {
            return true;
        }

        return false;
    }


    public function getEndTimeAttribute()
    {

        $dt = \DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 ' . $this->start_time);
        if (!$dt)
            $dt = \DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 00:00:00');


        $duration = $this->duration;

        if (is_null($duration))
            $duration = 0;

        $dt->add(new \DateInterval('PT' . $duration . 'S'));

        return $dt->format('H:i:s');
    }
    public function setEndTimeAttribute($endTime)
    {

        //duration
        $dtStart = \DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 ' . $this->start_time);
        if (!$dtStart)
            $dtStart = \DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 00:00:00');


        $dtEnd = \DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 ' . $endTime);
        if ($dtEnd < $dtStart) {
            $dtEnd->add(new \DateInterval("P1D"));
        }

        $this->duration = $dtEnd->getTimestamp() - $dtStart->getTimestamp();
    }

    public function getOverTimeAttribute()
    {

        $dt = \DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 ' . $this->start_time);
        if (!$dt)
            $dt = \DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 00:00:00');


        $duration = $this->normal_duration;

        if (is_null($duration))
            $duration = 0;

        $dt->add(new \DateInterval('PT' . $duration . 'S'));

        return $dt->format('H:i:s');
    }
    public function setOverTimeAttribute($endTime)
    {

        //duration
        $dtStart = \DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 ' . $this->start_time);
        if (!$dtStart)
            $dtStart = \DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 00:00:00');


        $dtEnd = \DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 ' . $endTime);
        if ($dtEnd < $dtStart) {
            $dtEnd->add(new \DateInterval("P1D"));
        }

        $this->normal_duration = $dtEnd->getTimestamp() - $dtStart->getTimestamp();
    }


    public function setDuration($endTime, $startTime = null)
    {
        //Assume validated HH:mm:ss

        if (is_null($startTime))
            $startTime = $this->start_time;


        $this->start_time = $startTime;

        //duration
        $dtStart = \DateTime::createFromFormat('Y-m-d H:i', '2000-01-01 ' . $startTime);
        $dtEnd = \DateTime::createFromFormat('Y-m-d H:i', '2000-01-01 ' . $endTime);
        if ($dtEnd < $dtStart) {
            $dtEnd->add(new \DateInterval("P1D"));
        }

        //get duration
        $this->duration = $dtEnd->getTimestamp() - $dtStart->getTimestamp();

        return $this;
    }

    public function setNormalDuration($overTime, $startTime = null)
    {
        //Assume validated HH:mm:ss

        if (is_null($startTime))
            $startTime = $this->start_time;


        $this->start_time = $startTime;

        //duration
        $dtStart = \DateTime::createFromFormat('Y-m-d H:i', '2000-01-01 ' . $startTime);
        $dtEnd = \DateTime::createFromFormat('Y-m-d H:i', '2000-01-01 ' . $overTime);
        if ($dtEnd < $dtStart) {
            $dtEnd->add(new \DateInterval("P1D"));
        }

        //get duration
        $this->normal_duration = $dtEnd->getTimestamp() - $dtStart->getTimestamp();

        return $this;
    }

    public function setOvertimeLimit()
    {
        if ($this->normal_duration > $this->duration) {
            $this->normal_duration = $this->duration;
        }

        return $this;
    }

    public function getLastStartEndDateTime(\DateTime $now = null, $carbon = false)
    {
        /** @var \App\Models\Plant $plant */
        $plant = $this->plant;
        if ($now)
            $now->setTimezone($plant->getLocalDateTimeZone());
        elseif ($now == null)
            $now = $plant->getLocalDateTime();


        $currentDayOfWeek = $now->format('N');
        $dayOffset = $currentDayOfWeek - $this->day_of_week;
        if ($dayOffset < 0)
            $dayOffset += 7;

        $shiftDateTime = clone $now;
        $shiftDateTime->sub(new \DateInterval('P' . $dayOffset . 'D'));
        $startTime = \DateTime::createFromFormat('Y-m-d H:i:s', $shiftDateTime->format('Y-m-d') . ' ' . $this->start_time, $plant->getLocalDateTimeZone());

        $endTime = clone $startTime;
        $endTime->add(new \DateInterval('PT' . $this->duration . 'S'));

        $overTime = clone $startTime;
        $overTime->add(new \DateInterval('PT' . $this->normal_duration . 'S'));
        if (!$carbon)
            return ['start_time' => $startTime, 'over_time' => $overTime, 'end_time' => $endTime];
        else
            return ['start_time' => new Carbon($startTime->getTimestamp()), 'over_time' => new Carbon($overTime->getTimestamp()), 'end_time' => new Carbon($endTime->getTimestamp())];
    }

    public function isInsideShift($now = null)
    {
        /** @var \App\Models\Plant $plant */
        $plant = $this->plant;
        if ($now == null)
            $now = $plant->getLocalDateTime();

        $currentDayOfWeek = $now->format('N');
        $yesterDayOfWeek = $currentDayOfWeek - 1;
        if ($yesterDayOfWeek == 0) //wrap around
            $yesterDayOfWeek = 7;

        if (!($this->day_of_week != $yesterDayOfWeek || $this->day_of_week != $currentDayOfWeek))
            return false;

        $lastStartEnd = $this->getLastStartEndDateTime($now);


        return ($now >= $lastStartEnd['start_time'] && $now <= $lastStartEnd['end_time']);
    }
    //relationships

    //belongto plant_id
    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plant_id', 'id');
    }

    //belongto shift_type_id
    public function shiftType()
    {
        return $this->belongsTo(ShiftType::class, 'shift_type_id', 'id');
    }
}
