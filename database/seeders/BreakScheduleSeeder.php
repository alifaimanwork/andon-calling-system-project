<?php

namespace Database\Seeders;

use App\Models\Plant;
use App\Models\BreakSchedule;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BreakScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $break_schedules = ['R2A & R2G','Template 2'];

        /** @var \App\Models\Plant $plant */
        $plant = Plant::where('uid', 'iav-rayong')->first();
        $plant->loadAppDatabase();



        foreach ($break_schedules as $template) {
            (new BreakSchedule([
                'plant_id' => $plant->id,
                'name' => $template,
                'enabled' => 1,
            ]))->setConnection($plant->getPlantConnection())->save();
        }

        $workCenters = $plant->onPlantDb()->workCenters()->where('work_centers.uid', 'r2a')->orWhere('work_centers.uid', 'r2g')->get();

        //Quick Assign to Workcenter R2A & R2G
        $schedule = $plant->breakSchedules()->first();

        
        /** @var \App\Models\WorkCenter $workCenter */
        foreach($workCenters as $workCenter)
        {
            $workCenter->breakSchedule()->associate($schedule)->save();
        }
        
        $break_schedulesDua = ['SMI BREAK 1','template'];

        /** @var \App\Models\Plant $plantDua */
        $plantDua = Plant::where('uid', 'smi-beranang')->first();
        $plantDua->loadAppDatabase();



        foreach ($break_schedulesDua as $templateDua) {
            (new BreakSchedule([
                'plant_id' => $plantDua->id,
                'name' => $templateDua,
                'enabled' => 1,
            ]))->setConnection($plantDua->getPlantConnection())->save();
        }

        $workCentersDua = $plantDua->onPlantDb()->workCenters()->where('work_centers.uid', 'l8')->get();

        //Quick Assign to Workcenter L8
        $scheduleDua = $plantDua->breakSchedules()->first();

        
        /** @var \App\Models\WorkCenter $workCenterDua */
        foreach($workCentersDua as $workCenterDua)
        {
            $workCenterDua->breakSchedule()->associate($scheduleDua)->save();
        }
    }
}
