<?php

namespace App\Models;

use App\Models\BreakSchedule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned integer
 * @property int $break_schedule_id Foreign Key (breakSchedule): unsigned integer
 * 
 * @property string $name string
 * @property string $start_time time
 * @property string $duration unsigned integer
 * @property int $day_of_week Day of week ISO-8601: 1: Monday ... 7:Sunday
 * 
 * @property string $created_at timestamp
 * @property string $updated_at timestamp
 * 
 * @property string $end_time time
 */

class BreakTime extends Model
{
    const TABLE_NAME = 'break_times';
    protected $table = self::TABLE_NAME;

    protected $guarded = [];
    protected $appends = ['end_time'];
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

    public function getLastStartEndDateTime(\DateTime $now = null, $carbon = false,$localTimeZone = null)
    {
        /** @var \App\Models\Plant $plant */
        $plant = $this->plant;
        if ($now)
            $now->setTimezone($localTimeZone);
        elseif ($now == null)
            $now = $plant->getLocalDateTime();


        $currentDayOfWeek = $now->format('N');
        $dayOffset = $currentDayOfWeek - $this->day_of_week;
        if ($dayOffset < 0)
            $dayOffset += 7;

        $shiftDateTime = clone $now;
        $shiftDateTime->sub(new \DateInterval('P' . $dayOffset . 'D'));
        $startTime = \DateTime::createFromFormat('Y-m-d H:i:s', $shiftDateTime->format('Y-m-d') . ' ' . $this->start_time, $localTimeZone);

        $endTime = clone $startTime;
        $endTime->add(new \DateInterval('PT' . $this->duration . 'S'));

        if (!$carbon)
            return ['start_time' => $startTime, 'end_time' => $endTime];
        else
            return ['start_time' => new Carbon($startTime->getTimestamp()), 'end_time' => new Carbon($endTime->getTimestamp())];
    }
    //relationships

    //belongto break_schedule
    public function breakSchedule()
    {
        return $this->belongsTo(BreakSchedule::class, 'break_schedule_id', 'id');
    }
}
