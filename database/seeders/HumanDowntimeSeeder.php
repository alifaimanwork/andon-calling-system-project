<?php

namespace Database\Seeders;

use App\Models\Plant;
use App\Models\Downtime;
use App\Models\DowntimeType;
use App\Models\WorkCenterDowntime;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class HumanDowntimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $plants = Plant::get();

        /** @var \App\Models\Plant $plant */
        foreach ($plants as $plant) {
            $workCenters = $plant->onPlantDb()->workCenters()->get();
            if (count($workCenters) <= 0)
                continue;

            $downtimes = $plant->onPlantDb()->downtimes()->where('downtime_type_id', '=', DowntimeType::HUMAN_DOWNTIME)->get();
            $plantConnection = $plant->getPlantConnection();

            /** @var \App\Models\WorkCenter $workCenter */
            foreach ($workCenters as $workCenter) {
                /** @var \App\Models\Downtime $downtime */
                foreach ($downtimes as $downtime) {

                    //attach human downtime to workcenter
                    (new WorkCenterDowntime([
                        'work_center_id' => $workCenter->id,
                        'downtime_id' => $downtime->id,
                    ]))->setConnection($plantConnection)->save();
                }
            }
        }
    }
}
