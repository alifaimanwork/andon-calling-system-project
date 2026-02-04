<?php

namespace Database\Seeders;

use App\Models\Plant;
use App\Models\Downtime;
use App\Models\DowntimeType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DowntimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rowDefs_machine = [
            [
                'category' => 'Oil Press Machine #1',
            ],
            [
                'category' => 'Oil Press Machine #2',
            ],
            [
                'category' => 'Oil Press Machine #3',
            ],
            [
                'category' => 'Oil Press Machine #4',
            ],
            [
                'category' => 'Autoloader #1',
            ],
            [
                'category' => 'Autoloader #2',
            ],
            [
                'category' => 'Robot #1',
            ],
            [
                'category' => 'Robot #2',
            ],
            [
                'category' => 'Slider Base/Lifter #1',
            ],
            [
                'category' => 'SBN Machine',
            ],

            [
                'category' => 'Emergency',
            ],
        ];

        $rowDefs_human = [
            [
                'category' => 'WIP Waiting',
            ],
            [
                'category' => 'Material Shortage',
            ],
            [
                'category' => 'Pallet / FG Box Waiting',
            ],

        ];

        $plants_machine = [
            'iav-rayong' => $rowDefs_machine,
            'iav-ayutthaya' => $rowDefs_machine
        ];

        $plants_human = [
            'iav-rayong' => $rowDefs_human,
            'iav-ayutthaya' => $rowDefs_human
        ];

        foreach ($plants_machine as $plantUid => $items) {
            $plant = Plant::where('uid', $plantUid)->first();
            $plant->loadAppDatabase();
            $plantConnection = $plant->getPlantConnection();

            foreach ($items as $row) {
                $row['plant_id'] = $plant->id;
                $row['downtime_type_id'] = DowntimeType::on($plantConnection)->where('name', 'Machine / Tooling')->first()->id;
                $row['enabled'] = 1;

                (new Downtime($row))->setConnection($plantConnection)->save();
            }
        }

        foreach ($plants_human as $plantUid => $items) {
            $plant = Plant::where('uid', $plantUid)->first();
            $plant->loadAppDatabase();
            $plantConnection = $plant->getPlantConnection();

            foreach ($items as $row) {
                $row['plant_id'] = $plant->id;
                $row['downtime_type_id'] = DowntimeType::on($plantConnection)->where('name', 'Human Downtime')->first()->id;
                $row['enabled'] = 1;

                (new Downtime($row))->setConnection($plantConnection)->save();
            }
        }

        $rowDefs_machineDua = [

            [
                'category' => 'Emergency',
            ],
        ];

        $rowDefs_humanDua = [
            [
                'category' => 'WIP Waiting',
            ],
            [
                'category' => 'Material Shortage',
            ],
            [
                'category' => 'Pallet / FG Box Waiting',
            ],

        ];

        $plants_machineDua = [
            'smi-beranang' => $rowDefs_machineDua
        ];

        $plants_human = [
            'smi-beranang' => $rowDefs_humanDua
        ];

        foreach ($plants_machineDua as $plantUidDua => $itemsDua) {
            $plantDua = Plant::where('uid', $plantUidDua)->first();
            $plantDua->loadAppDatabase();
            $plantConnectionDua = $plantDua->getPlantConnection();

            foreach ($itemsDua as $rowDua) {
                $rowDua['plant_id'] = $plantDua->id;
                $rowDua['downtime_type_id'] = DowntimeType::on($plantConnectionDua)->where('name', 'Machine / Tooling')->first()->id;
                $rowDua['enabled'] = 1;

                (new Downtime($rowDua))->setConnection($plantConnectionDua)->save();
            }
        }

        foreach ($plants_human as $plantUidDua => $itemsDua) {
            $plantDua = Plant::where('uid', $plantUidDua)->first();
            $plantDua->loadAppDatabase();
            $plantConnectionDua = $plantDua->getPlantConnection();

            foreach ($itemsDua as $rowDua) {
                $rowDua['plant_id'] = $plantDua->id;
                $rowDua['downtime_type_id'] = DowntimeType::on($plantConnectionDua)->where('name', 'Human Downtime')->first()->id;
                $rowDua['enabled'] = 1;

                (new Downtime($rowDua))->setConnection($plantConnectionDua)->save();
            }
        }
    }
}
