<?php

namespace Database\Seeders;

use App\Models\Plant;
use App\Models\BreakSchedule;
use App\Models\BreakTime;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BreakTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dailySchedule = [
            [
                'day_of_week' => 1,
                'start_time' => '10:00:00',
                'duration' => 900
            ],
            [
                'day_of_week' => 1,
                'start_time' => '12:15:00',
                'duration' => 2700
            ],
            [
                'day_of_week' => 1,
                'start_time' => '15:00:00',
                'duration' => 600
            ],
            [
                'day_of_week' => 1,
                'start_time' => '16:50:00',
                'duration' => 1800
            ],
            [
                'day_of_week' => 1,
                'start_time' => '22:00:00',
                'duration' => 900
            ],
            [
                'day_of_week' => 1,
                'start_time' => '00:00:00',
                'duration' => 3600
            ],
            [
                'day_of_week' => 1,
                'start_time' => '03:00:00',
                'duration' => 600
            ],
            [
                'day_of_week' => 1,
                'start_time' => '05:00:00',
                'duration' => 1800
            ],
        ];

        $breakTimes = [];

        for($n = 1;$n<=7;$n++)
        {
            foreach($dailySchedule as $slot)
            {
                $breakTimes[] = [
                    'day_of_week' => $n,
                    'start_time' => $slot['start_time'],
                    'duration' => $slot['duration']
                ];    
            }
            
        }

        /** @var \App\Models\Plant $plant */
        $plant = Plant::where('uid', 'iav-rayong')->first();
        $plant->loadAppDatabase();

        $plantConnection = $plant->getPlantConnection();
        $schedule = $plant->onPlantDb()->breakSchedules()->first();

        foreach ($breakTimes as $breakTime) {

            (new BreakTime(
                array_merge(
                    ['break_schedule_id' => $schedule->id],
                    $breakTime
                )
            ))->setConnection($plantConnection)->save();
        }

        $dailyScheduleDua = [
            [
                'day_of_week' => 1,
                'start_time' => '10:00:00',
                'duration' => 900
            ],
            [
                'day_of_week' => 1,
                'start_time' => '12:15:00',
                'duration' => 2700
            ],
            [
                'day_of_week' => 1,
                'start_time' => '15:00:00',
                'duration' => 600
            ],
            [
                'day_of_week' => 1,
                'start_time' => '16:50:00',
                'duration' => 1800
            ],
            [
                'day_of_week' => 1,
                'start_time' => '22:00:00',
                'duration' => 900
            ],
            [
                'day_of_week' => 1,
                'start_time' => '00:00:00',
                'duration' => 3600
            ],
            [
                'day_of_week' => 1,
                'start_time' => '03:00:00',
                'duration' => 600
            ],
            [
                'day_of_week' => 1,
                'start_time' => '05:00:00',
                'duration' => 1800
            ],
        ];

        $breakTimesDua = [];

        for($nDua = 1;$nDua<=7;$nDua++)
        {
            foreach($dailyScheduleDua as $slotDua)
            {
                $breakTimesDua[] = [
                    'day_of_week' => $nDua,
                    'start_time' => $slotDua['start_time'],
                    'duration' => $slotDua['duration']
                ];    
            }
            
        }

        /** @var \App\Models\Plant $plantDua */
        $plantDua = Plant::where('uid', 'smi-beranang')->first();
        $plantDua->loadAppDatabase();

        $plantConnectionDua = $plantDua->getPlantConnection();
        $scheduleDua = $plantDua->onPlantDb()->breakSchedules()->first();

        foreach ($breakTimesDua as $breakTimeDua) {

            (new BreakTime(
                array_merge(
                    ['break_schedule_id' => $scheduleDua->id],
                    $breakTimeDua
                )
            ))->setConnection($plantConnectionDua)->save();
        }
    }
}
