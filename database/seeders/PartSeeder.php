<?php

namespace Database\Seeders;

use App\Models\Part;
use App\Models\Plant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rayong = Plant::where('uid','iav-rayong')->first();
        $workCenterR2G = $rayong->onPlantDb()->workCenters()->where('work_centers.uid','r2g')->first();
        $workCenterR1G = $rayong->onPlantDb()->workCenters()->where('work_centers.uid','r1g')->first();

        $workCenterR2A = $rayong->onPlantDb()->workCenters()->where('work_centers.uid','r2a')->first();

        $defaultSetupTime = 30;
        $defaultCycleTime = 15;
        //
        $partsDef = [
            [
                'name' => 'CHAN FR DR WDO GL, RH',
                'work_center_id' => $workCenterR2G->id,
                'line_no'=> 1,
                'setup_time' => $defaultSetupTime,
                'cycle_time' => $defaultCycleTime,
                'packaging' => 100,
                'reject_target' => 0.1,
                'side' => 'RH',
                'enabled' => 1,
                'opc_part_id' => 0,
                'part_no' => 'AB39-2621468-BC_'
            ],
            [
                'name' => 'CHAN FR DR WDO GL, LH',
                'work_center_id' => $workCenterR2G->id,
                'line_no'=> 2,
                'setup_time' => $defaultSetupTime,
                'cycle_time' => $defaultCycleTime,
                'packaging' => 100,
                'reject_target' => 0.1,
                'side' => 'LH',
                'enabled' => 1,
                'opc_part_id' => 0,
                'part_no' => 'AB39-2621469-BC_'
            ],
            [
                'name' => 'CHAN FRT DR WDO GL, RH',
                'work_center_id' => $workCenterR2G->id,
                'line_no'=> 1,
                'setup_time' => $defaultSetupTime,
                'cycle_time' => $defaultCycleTime,
                'packaging' => 100,
                'reject_target' => 0.1,
                'side' => 'RH',
                'enabled' => 1,
                'opc_part_id' => 0,
                'part_no' => 'N1WB-E21468-AA_'
            ],
            [
                'name' => 'CHAN FRT DR WDO GL, LH',
                'work_center_id' => $workCenterR2G->id,
                'line_no'=> 2,
                'setup_time' => $defaultSetupTime,
                'cycle_time' => $defaultCycleTime,
                'packaging' => 100,
                'reject_target' => 0.1,
                'side' => 'LH',
                'enabled' => 1,
                'opc_part_id' => 0,
                'part_no' => 'N1WB-E21469-AA_'
            ],
            [
                'name' => 'CHAN FR DR WDO GL, RH',
                'work_center_id' => $workCenterR2G->id,
                'line_no'=> 1,
                'setup_time' => $defaultSetupTime,
                'cycle_time' => $defaultCycleTime,
                'packaging' => 100,
                'reject_target' => 0.1,
                'side' => 'RH',
                'enabled' => 1,
                'opc_part_id' => 0,
                'part_no' => 'AB39-2121468-BC_'
            ],
            [
                'name' => 'CHAN FR DR WDO GL, LH',
                'work_center_id' => $workCenterR2G->id,
                'line_no'=> 2,
                'setup_time' => $defaultSetupTime,
                'cycle_time' => $defaultCycleTime,
                'packaging' => 100,
                'reject_target' => 0.1,
                'side' => 'LH',
                'enabled' => 1,
                'opc_part_id' => 0,
                'part_no' => 'AB39-2121469-BC_'
            ],
            [
                'name' => 'MOULDING, FR/DR B/LINE OTR, LH',
                'work_center_id' => $workCenterR1G->id,
                'line_no'=> 1,
                'setup_time' => $defaultSetupTime,
                'cycle_time' => $defaultCycleTime,
                'packaging' => 100,
                'reject_target' => 0.1,
                'side' => 'LH',
                'enabled' => 1,
                'opc_part_id' => 0,
                'part_no' => '5727A267_'
            ],
            [
                'name' => 'CHAN-RR DOOR, FR RH',
                'work_center_id' => $workCenterR2A->id,
                'line_no'=> 1,
                'setup_time' => $defaultSetupTime,
                'cycle_time' => $defaultCycleTime,
                'packaging' => 100,
                'reject_target' => 0.1,
                'side' => 'LH',
                'enabled' => 1,
                'opc_part_id' => 0,
                'part_no' => '822244JG0A_'
            ]
        ];


        $plants = [
            'iav-rayong' => $partsDef
        ];

        foreach ($plants as $plantUid => $parts) {
            $plant = Plant::where('uid', $plantUid)->first();
            $plant->loadAppDatabase();
            $plantConnection = $plant->getPlantConnection();

            foreach ($parts as $part) {
                $part['plant_id'] = $plant->id;
                (new Part($part))->setConnection($plantConnection)->save();
            }
        }

        $beranang = Plant::where('uid','smi-beranang')->first();
        $workCenterL8 = $beranang->onPlantDb()->workCenters()->where('work_centers.uid','l8')->first();

        $defaultSetupTimeDua = 30;
        $defaultCycleTimeDua = 15;
        //
        $partsDefDua = [
            [
                'name' => 'Hinge FR Dr Upr LH',
                'work_center_id' => $workCenterL8->id,
                'line_no'=> 1,
                'setup_time' => $defaultSetupTimeDua,
                'cycle_time' => $defaultCycleTimeDua,
                'packaging' => 100,
                'reject_target' => 0.1,
                'side' => 'LH',
                'enabled' => 1,
                'opc_part_id' => 0,
                'part_no' => 'PW935285'
            ],
            [
                'name' => 'Hinge FR Dr Upr RH',
                'work_center_id' => $workCenterL8->id,
                'line_no'=> 1,
                'setup_time' => $defaultSetupTimeDua,
                'cycle_time' => $defaultCycleTimeDua,
                'packaging' => 100,
                'reject_target' => 0.1,
                'side' => 'RH',
                'enabled' => 1,
                'opc_part_id' => 0,
                'part_no' => 'PW935286'
            ],
            [
                'name' => 'Hinge FR Dr Lwr LH',
                'work_center_id' => $workCenterL8->id,
                'line_no'=> 1,
                'setup_time' => $defaultSetupTimeDua,
                'cycle_time' => $defaultCycleTimeDua,
                'packaging' => 100,
                'reject_target' => 0.1,
                'side' => 'LH',
                'enabled' => 1,
                'opc_part_id' => 0,
                'part_no' => 'PW935289'
            ],
            [
                'name' => 'Hinge FR Dr Lwr RH',
                'work_center_id' => $workCenterL8->id,
                'line_no'=> 1,
                'setup_time' => $defaultSetupTimeDua,
                'cycle_time' => $defaultCycleTimeDua,
                'packaging' => 100,
                'reject_target' => 0.1,
                'side' => 'RH',
                'enabled' => 1,
                'opc_part_id' => 0,
                'part_no' => 'PW935290'
            ]
        ];


        $plantsDua = [
            'smi-beranang' => $partsDefDua
        ];

        foreach ($plantsDua as $plantUidDua => $partsDua) {
            $plantDua = Plant::where('uid', $plantUidDua)->first();
            $plantDua->loadAppDatabase();
            $plantConnectionDua = $plantDua->getPlantConnection();

            foreach ($partsDua as $partDua) {
                $partDua['plant_id'] = $plantDua->id;
                (new Part($partDua))->setConnection($plantConnectionDua)->save();
            }
        }
    }
}
