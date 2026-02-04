<?php

namespace App\Models;

use App\Models\BreakTime;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned integer
 * 
 * @property int $plant_id Foreign Key (plant): unsigned integer
 * 
 * @property int $name string
 * @property int $enabled tinyinteger (0: Disabled, 1: Enabled)
 * 
 * @property string $created_at timestamp
 * @property string $updated_at timestamp
 * 
 */
class BreakSchedule extends Model
{
    const TABLE_NAME = 'break_schedules';
    protected $table = self::TABLE_NAME;



    public function getBreaktimeBetween(\DateTime $startTime, \DateTime $endTime, $carbon = false, $clipStartEnd = true)
    {
        //Condition: Assume startTime & endTime duration not more than 1 week

        /** @var \App\Models\Plant $plant */
        $plant = $this->plant;

        $localStartTime = $startTime->setTimezone($plant->getLocalDateTimeZone());
        $localEndTime = $endTime->setTimezone($plant->getLocalDateTimeZone());

        //Get breaktime between start & end time
        $breakTimes = $this->breakTimes()->get();



        $breakTimeSlots = [];
        /** @var \App\Models\BreakTime $breakTime */
        foreach ($breakTimes as $breakTime) {
            $breakTimeSlots[] = $breakTime->getLastStartEndDateTime($localEndTime, false, $plant->getLocalDateTimeZone());
        }
        
        $filteredSlots = [];
        foreach ($breakTimeSlots as $timeSlot) {
            //only include if overlap with startTime & endTime
            if (($timeSlot['start_time'] > $localStartTime && $timeSlot['start_time'] < $localEndTime) ||
                ($timeSlot['end_time'] > $localStartTime && $timeSlot['end_time'] < $localEndTime) ||
                ($localStartTime > $timeSlot['start_time'] && $localStartTime < $timeSlot['end_time']) ||
                ($localEndTime > $timeSlot['start_time'] && $localEndTime < $timeSlot['end_time']) ||
                ($localStartTime == $timeSlot['start_time'] && $localEndTime == $timeSlot['end_time'])
            )
                $filteredSlots[] = $timeSlot;
        }
        
        //clip start end
        if ($clipStartEnd) {
            foreach ($filteredSlots as &$filteredSlot) {
                if ($filteredSlot['start_time'] < $localStartTime)
                    $filteredSlot['start_time'] = clone $localStartTime;

                if ($filteredSlot['end_time'] > $localEndTime)
                    $filteredSlot['end_time'] = clone $localEndTime;
            }
            unset($filteredSlot);
        }
        
        //Merge
        for ($i = 0; $i < count($filteredSlots); $i++) {
            for ($j = 0; $j < count($filteredSlots); $j++) {
                if ($i == $j)
                    continue;



                if ($filteredSlots[$j]['start_time'] == null || $filteredSlots[$j]['end_time'] == null)
                    continue;

                if ($filteredSlots[$j]['start_time'] == $filteredSlots[$j]['end_time']) {
                    //mark merged
                    $filteredSlots[$j]['start_time'] = null;
                    $filteredSlots[$j]['end_time'] = null;
                }


                if (($filteredSlots[$i]['start_time'] > $filteredSlots[$j]['start_time'] && $filteredSlots[$i]['start_time'] < $filteredSlots[$j]['end_time']) ||
                    ($filteredSlots[$i]['end_time'] > $filteredSlots[$j]['end_time'] && $filteredSlots[$i]['end_time'] < $filteredSlots[$j]['end_time']) ||
                    ($filteredSlots[$j]['start_time'] > $filteredSlots[$i]['start_time'] && $filteredSlots[$j]['start_time'] < $filteredSlots[$i]['end_time']) ||
                    ($filteredSlots[$j]['end_time'] > $filteredSlots[$i]['end_time'] && $filteredSlots[$j]['end_time'] < $filteredSlots[$i]['end_time']) ||
                    ($filteredSlots[$i]['start_time'] == $filteredSlots[$j]['start_time'] && $filteredSlots[$i]['end_time'] == $filteredSlots[$j]['end_time'])
                ) {
                    //merge
                    $filteredSlots[$i]['start_time'] = clone ($filteredSlots[$i]['start_time'] < $filteredSlots[$j]['start_time'] ? $filteredSlots[$i]['start_time'] : $filteredSlots[$j]['start_time']);
                    $filteredSlots[$i]['end_time'] = clone ($filteredSlots[$i]['end_time'] > $filteredSlots[$j]['end_time'] ? $filteredSlots[$i]['end_time'] : $filteredSlots[$j]['end_time']);

                    //mark merged
                    $filteredSlots[$j]['start_time'] = null;
                    $filteredSlots[$j]['end_time'] = null;
                }
            }
        }
        $mergedSlots = [];

        foreach ($filteredSlots as $filteredSlot) {
            if ($filteredSlot['start_time'] == null || $filteredSlot['end_time'] == null)
                continue;

            $mergedSlots[] = $filteredSlot;
        }


        //reorder
        usort($mergedSlots, function ($a, $b) {
            return $a['start_time'] <=> $b['start_time'];
        });

        //Carbon output
        if ($carbon) {
            foreach ($mergedSlots as &$mergedSlot) {
                $mergedSlot['start_time'] = new Carbon($mergedSlot['start_time']->getTimestamp());
                $mergedSlot['end_time'] = new Carbon($mergedSlot['end_time']->getTimestamp());
                unset($mergedSlot);
            }
        }
        
        return $mergedSlots;
    }



    //relationships

    //hasmany work_centers
    public function workCenters()
    {
        return $this->hasMany(WorkCenter::class, 'break_schedule_id', 'id');
    }

    //hasmany breaks
    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class, 'break_schedule_id', 'id');
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plant_id', 'id');
    }
}
