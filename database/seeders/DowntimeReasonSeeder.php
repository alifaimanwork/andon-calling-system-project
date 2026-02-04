<?php

namespace Database\Seeders;

use App\Models\Reason;
use App\Models\Downtime;
use App\Models\DowntimeReason;
use App\Models\Plant;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DowntimeReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //TODO: load iav-rayong plant
        $plant = Plant::where('uid', 'iav-rayong')->first();
        $plant->loadAppDatabase();

        //query downtime mana
        $rowDef = [
            [
                // 'id' => 1,
                'reason' => 'TOOLING FAILURE',
                'enable_user_input' => 0,
                'enabled' => 1,
            ],
            [
                // 'id' => 2,
                'reason' => 'CONNECTOR FAILURE',
                'enable_user_input' => 0,
                'enabled' => 1,
            ],
            [
                // 'id' => 3,
                'reason' => 'HYDRAULIC PUMP FAILURE',
                'enable_user_input' => 0,
                'enabled' => 0,
            ],
            [
                // 'id' => 4,
                'reason' => 'SENSOR FAILURE',
                'enable_user_input' => 1,
                'enabled' => 0,
            ],
            [
                // 'id' => 5,
                'reason' => 'CPU CARD FAILURE',
                'enable_user_input' => 0,
                'enabled' => 0,
            ],
        ];


        $downtimes = Downtime::on($plant->getPlantConnection())->get();


        foreach ($downtimes as $downtime) {
            foreach ($rowDef as $row) {
                (new DowntimeReason(
                    array_merge(
                        $row,
                        [
                            'downtime_id' => $downtime->id,
                        ]
                    )
                ))->setConnection($plant->getPlantConnection())->save();
            }
        }
        /// Downtime::on($plant->getPlantConnection)//


        // pivot table, attach to downtime_id and reason_id

        // foreach (Downtime::all() as $downtime) {
        //     foreach (Reason::all() as $reason) {
        //         $downtime->reasons()->attach($reason->id);
        //     }
        // }
    }
}
