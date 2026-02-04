<?php

namespace Database\Seeders;

use App\Models\DashboardLayout;
use App\Models\Factory;
use App\Models\Plant;
use App\Models\WorkCenter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $default_threshold = 0.85;
        $rayongPlant = Plant::where('uid', 'iav-rayong')->first();
        $rayongPlant->loadAppDatabase();
        $rayongFactories = Factory::on($rayongPlant->getPlantConnection())->pluck('id', 'name')->toArray();

        $testPath = (object)[
            'pps' => base_path('storage' . DIRECTORY_SEPARATOR . 'sap_test' . DIRECTORY_SEPARATOR . 'pps'),
            'gr_ok' => base_path('storage' . DIRECTORY_SEPARATOR . 'sap_test' . DIRECTORY_SEPARATOR . 'grok'),
            'gr_ng' => base_path('storage' . DIRECTORY_SEPARATOR . 'sap_test' . DIRECTORY_SEPARATOR . 'grng'),
            'gr_qi' => base_path('storage' . DIRECTORY_SEPARATOR . 'sap_test' . DIRECTORY_SEPARATOR . 'grqi'),
            'rw_ok' => base_path('storage' . DIRECTORY_SEPARATOR . 'sap_test' . DIRECTORY_SEPARATOR . 'rwok'),
            'rw_ng' => base_path('storage' . DIRECTORY_SEPARATOR . 'sap_test' . DIRECTORY_SEPARATOR . 'rwng'),
            'ett_op10' => base_path('storage' . DIRECTORY_SEPARATOR . 'sap_test' . DIRECTORY_SEPARATOR . 'ettop10'),
            'ett_op20' => base_path('storage' . DIRECTORY_SEPARATOR . 'sap_test' . DIRECTORY_SEPARATOR . 'ettop20'),

        ];

        //
        $plants = [
            'iav-rayong' => [
                [
                    'uid' => 'r1g',
                    'name' => 'R1G',
                    'factory_id' => $rayongFactories['Factory 1'],
                    'production_line_count' => 1,
                    'pps_source' => $testPath->pps,
                    'gr_ok_destination' => $testPath->gr_ok,
                    'gr_ng_destination' => $testPath->gr_ng,
                    'gr_qi_destination' => $testPath->gr_qi,

                    'rw_ok_destination' => $testPath->rw_ok,
                    'rw_ng_destination' => $testPath->rw_ng,

                    'ett10_destination' => $testPath->ett_op10,
                    'ett20_destination' => $testPath->ett_op20,
                    'enabled' => 1,

                    'threshold_oee' => $default_threshold,
                    'threshold_availability' => $default_threshold,
                    'threshold_performance' => $default_threshold,
                    'threshold_quality' => $default_threshold,
                ],
                [
                    'uid' => 'r2a',
                    'name' => 'R2A',
                    'factory_id' => $rayongFactories['Factory 2'],
                    'production_line_count' => 2,
                    'pps_source' => $testPath->pps,
                    'gr_ok_destination' => $testPath->gr_ok,
                    'gr_ng_destination' => $testPath->gr_ng,
                    'gr_qi_destination' => $testPath->gr_qi,

                    'rw_ok_destination' => $testPath->rw_ok,
                    'rw_ng_destination' => $testPath->rw_ng,

                    'ett10_destination' => $testPath->ett_op10,
                    'ett20_destination' => $testPath->ett_op20,
                    'enabled' => 1,

                    'threshold_oee' => $default_threshold,
                    'threshold_availability' => $default_threshold,
                    'threshold_performance' => $default_threshold,
                    'threshold_quality' => $default_threshold,
                ],
                [
                    'uid' => 'r2g',
                    'name' => 'R2G',
                    'factory_id' => $rayongFactories['Factory 2'],
                    'production_line_count' => 2,
                    'pps_source' => $testPath->pps,
                    'gr_ok_destination' => $testPath->gr_ok,
                    'gr_ng_destination' => $testPath->gr_ng,
                    'gr_qi_destination' => $testPath->gr_qi,

                    'rw_ok_destination' => $testPath->rw_ok,
                    'rw_ng_destination' => $testPath->rw_ng,

                    'ett10_destination' => $testPath->ett_op10,
                    'ett20_destination' => $testPath->ett_op20,
                    'enabled' => 1,

                    'threshold_oee' => $default_threshold,
                    'threshold_availability' => $default_threshold,
                    'threshold_performance' => $default_threshold,
                    'threshold_quality' => $default_threshold,
                ],
                [
                    'uid' => 'r3u',
                    'name' => 'R3U',
                    'factory_id' => $rayongFactories['Factory 3'],
                    'production_line_count' => 6,
                    'pps_source' => $testPath->pps,
                    'gr_ok_destination' => $testPath->gr_ok,
                    'gr_ng_destination' => $testPath->gr_ng,
                    'gr_qi_destination' => $testPath->gr_qi,

                    'rw_ok_destination' => $testPath->rw_ok,
                    'rw_ng_destination' => $testPath->rw_ng,

                    'ett10_destination' => $testPath->ett_op10,
                    'ett20_destination' => $testPath->ett_op20,
                    'enabled' => 1,

                    'threshold_oee' => $default_threshold,
                    'threshold_availability' => $default_threshold,
                    'threshold_performance' => $default_threshold,
                    'threshold_quality' => $default_threshold,
                ],
                [
                    'uid' => 'r3s',
                    'name' => 'R3S',
                    'factory_id' => $rayongFactories['Factory 3'],
                    'production_line_count' => 6,
                    'pps_source' => $testPath->pps,
                    'gr_ok_destination' => $testPath->gr_ok,
                    'gr_ng_destination' => $testPath->gr_ng,
                    'gr_qi_destination' => $testPath->gr_qi,

                    'rw_ok_destination' => $testPath->rw_ok,
                    'rw_ng_destination' => $testPath->rw_ng,

                    'ett10_destination' => $testPath->ett_op10,
                    'ett20_destination' => $testPath->ett_op20,
                    'enabled' => 1,

                    'threshold_oee' => $default_threshold,
                    'threshold_availability' => $default_threshold,
                    'threshold_performance' => $default_threshold,
                    'threshold_quality' => $default_threshold,
                ]
            ]
        ];
        foreach ($plants as $plantUid => $workCenters) {
            $plant = Plant::where('uid', $plantUid)->first();
            $plant->loadAppDatabase();
            $plantConnection = $plant->getPlantConnection();
            $layoutIds = DashboardLayout::pluck('id', 'capacity')->toArray();

            foreach ($workCenters as $workCenter) {
                $workCenter['plant_id'] = $plant->id;
                $workCenter['dashboard_layout_id'] = $layoutIds[$workCenter['production_line_count']];
                (new WorkCenter($workCenter))->setConnection($plantConnection)->save();
            }
        }

        $default_thresholdDua = 0.85;
        $beranangPlant = Plant::where('uid', 'smi-beranang')->first();
        $beranangPlant->loadAppDatabase();
        $beranangFactories = Factory::on($beranangPlant->getPlantConnection())->pluck('id', 'name')->toArray();

        $testPathDua = (object)[
            'pps' => base_path('storage' . DIRECTORY_SEPARATOR . 'sap_test' . DIRECTORY_SEPARATOR . 'pps'),
            'gr_ok' => base_path('storage' . DIRECTORY_SEPARATOR . 'sap_test' . DIRECTORY_SEPARATOR . 'grok'),
            'gr_ng' => base_path('storage' . DIRECTORY_SEPARATOR . 'sap_test' . DIRECTORY_SEPARATOR . 'grng'),
            'gr_qi' => base_path('storage' . DIRECTORY_SEPARATOR . 'sap_test' . DIRECTORY_SEPARATOR . 'grqi'),
            'rw_ok' => base_path('storage' . DIRECTORY_SEPARATOR . 'sap_test' . DIRECTORY_SEPARATOR . 'rwok'),
            'rw_ng' => base_path('storage' . DIRECTORY_SEPARATOR . 'sap_test' . DIRECTORY_SEPARATOR . 'rwng'),
            'ett_op10' => base_path('storage' . DIRECTORY_SEPARATOR . 'sap_test' . DIRECTORY_SEPARATOR . 'ettop10'),
            'ett_op20' => base_path('storage' . DIRECTORY_SEPARATOR . 'sap_test' . DIRECTORY_SEPARATOR . 'ettop20'),

        ];

        //
        $plantsDua = [
            'smi-beranang' => [
                [
                    'uid' => 'l8',
                    'name' => 'L8',
                    'factory_id' => $beranangFactories['SMI Factory 1'],
                    'production_line_count' => 1,
                    'pps_source' => $testPathDua->pps,
                    'gr_ok_destination' => $testPathDua->gr_ok,
                    'gr_ng_destination' => $testPathDua->gr_ng,
                    'gr_qi_destination' => $testPathDua->gr_qi,

                    'rw_ok_destination' => $testPathDua->rw_ok,
                    'rw_ng_destination' => $testPathDua->rw_ng,

                    'ett10_destination' => $testPathDua->ett_op10,
                    'ett20_destination' => $testPathDua->ett_op20,
                    'enabled' => 1,

                    'threshold_oee' => $default_thresholdDua,
                    'threshold_availability' => $default_thresholdDua,
                    'threshold_performance' => $default_thresholdDua,
                    'threshold_quality' => $default_thresholdDua,
                ]
            ]
        ];
        foreach ($plantsDua as $plantUidDua => $workCentersDua) {
            $plantDua = Plant::where('uid', $plantUidDua)->first();
            $plantDua->loadAppDatabase();
            $plantConnectionDua = $plantDua->getPlantConnection();
            $layoutIdsDua = DashboardLayout::pluck('id', 'capacity')->toArray();

            foreach ($workCentersDua as $workCenterDua) {
                $workCenterDua['plant_id'] = $plantDua->id;
                $workCenterDua['dashboard_layout_id'] = $layoutIdsDua[$workCenterDua['production_line_count']];
                (new WorkCenter($workCenterDua))->setConnection($plantConnectionDua)->save();
            }
        }
    }
}
