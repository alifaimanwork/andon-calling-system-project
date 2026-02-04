<?php

namespace Database\Seeders;

use App\Models\OpcServer;
use App\Models\OpcTag;
use App\Models\OpcTagType;
use App\Models\Plant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OpcTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** @var \App\Models\Plant $plant */
        $plant = Plant::where('uid', '=', 'iav-rayong')->first();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        /** @var \App\Models\OpcServer $opcServer */
        $opcServer = OpcServer::first();




        $tagTypes = OpcTagType::pluck('id', 'name');

        /** @var \App\Models\WorkCenter $workCenter_r2a */
        $workCenter_r2a = $plant->onPlantDb()->workCenters()->where('work_centers.uid', '=', 'r2a')->first();
        $workCenter_r2g = $plant->onPlantDb()->workCenters()->where('work_centers.uid', '=', 'r2g')->first();


        $tags = [

            //R2A
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2a->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=R2A.AutoLoader 2',
                'opc_tag_type_id' => $tagTypes['Downtime'],
                'downtime_category' => 'Autoloader #2'
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2a->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=R2A.Break',
                'opc_tag_type_id' => $tagTypes['Break']
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2a->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=R2A.Die change',
                'opc_tag_type_id' => $tagTypes['Die Change']
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2a->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=R2A.Emergency',
                'opc_tag_type_id' => $tagTypes['Downtime'],
                'downtime_category' => 'Emergency'
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2a->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=R2A.Oil Press1',
                'opc_tag_type_id' => $tagTypes['Downtime'],
                'downtime_category' => 'Oil Press Machine #1'
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2a->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=R2A.Oil Press2',
                'opc_tag_type_id' => $tagTypes['Downtime'],
                'downtime_category' => 'Oil Press Machine #2'
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2a->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=R2A.Part Number Data1',
                'opc_tag_type_id' => $tagTypes['Part Number'],
                'info' => '1'
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2a->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=R2A.Part Number Data2',
                'opc_tag_type_id' => $tagTypes['Part Number'],
                'info' => '2'
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2a->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=R2A.Part Number Data3',
                'opc_tag_type_id' => $tagTypes['Part Number'],
                'info' => '3'
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2a->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=R2A.Part Number Data4',
                'opc_tag_type_id' => $tagTypes['Part Number'],
                'info' => '4'
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2a->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=R2A.Prod Count 1 data',
                'opc_tag_type_id' => $tagTypes['Counter'],
                'info' => '1'
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2a->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=R2A.Prod Count 2 data',
                'opc_tag_type_id' => $tagTypes['Counter'],
                'info' => '2'
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2a->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=R2A.Robot 1',
                'opc_tag_type_id' => $tagTypes['Downtime'],
                'downtime_category' => 'Robot #1'
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2a->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=R2A.Robot 2',
                'opc_tag_type_id' => $tagTypes['Downtime'],
                'downtime_category' => 'Robot #2'
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2a->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=R2A.Robot 3',
                'opc_tag_type_id' => $tagTypes['Downtime'],
                'downtime_category' => 'Robot #3'
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2a->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=R2A.Robot 4',
                'opc_tag_type_id' => $tagTypes['Downtime'],
                'downtime_category' => 'Robot #4'
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2a->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=R2A.SBN',
                'opc_tag_type_id' => $tagTypes['Downtime'],
                'downtime_category' => 'SBN Machine'
            ],

            //R2G
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2g->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=IAV T6.Auto Loader 1',
                'opc_tag_type_id' => $tagTypes['Downtime'],
                'downtime_category' => 'Autoloader #1',
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2g->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=IAV T6.Press 2',
                'opc_tag_type_id' => $tagTypes['Downtime'],
                'downtime_category' => 'Oil Press Machine #2',
            ],

            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2g->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=IAV T6.Press 3',
                'opc_tag_type_id' => $tagTypes['Downtime'],
                'downtime_category' => 'Oil Press Machine #3',
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2g->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=IAV T6.Press 4',
                'opc_tag_type_id' => $tagTypes['Downtime'],
                'downtime_category' => 'Oil Press Machine #4',
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2g->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=IAV T6.Die change',
                'opc_tag_type_id' => $tagTypes['Die Change']
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2g->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=IAV T6.AutoLoader 2',
                'opc_tag_type_id' => $tagTypes['Downtime'],
                'downtime_category' => 'Autoloader #2',
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2g->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=IAV T6.Part Number Data2',
                'opc_tag_type_id' => $tagTypes['Part Number'],
                'info' => '2'
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2g->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=IAV T6.Part Number Data3',
                'opc_tag_type_id' => $tagTypes['Part Number'],
                'info' => '3'
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2g->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=IAV T6.Part Number Data1',
                'opc_tag_type_id' => $tagTypes['Part Number'],
                'info' => '1'
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2g->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=IAV T6.Part Number Data4',
                'opc_tag_type_id' => $tagTypes['Part Number'],
                'info' => '4'
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2g->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=IAV T6.Prod Count 2 data',
                'opc_tag_type_id' => $tagTypes['Counter'],
                'info' => '2'
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2g->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=IAV T6.Prod Count 1 data',
                'opc_tag_type_id' => $tagTypes['Counter'],
                'info' => '1'
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2g->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=IAV T6.Press 1',
                'opc_tag_type_id' => $tagTypes['Downtime'],
                'downtime_category' => 'Oil Press Machine #1',
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2g->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=IAV T6.Emergency',
                'opc_tag_type_id' => $tagTypes['Downtime'],
                'downtime_category' => 'Emergency',
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2g->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=IAV T6.Break',
                'opc_tag_type_id' => $tagTypes['Break']
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2g->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=IAV T6.SBN',
                'opc_tag_type_id' => $tagTypes['Downtime'],
                'downtime_category' => 'SBN Machine',
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2g->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=IAV T6.Robot 2',
                'opc_tag_type_id' => $tagTypes['Downtime'],
                'downtime_category' => 'Robot #2',
            ],
            [
                'plant_id' => $plant->id,
                'work_center_id' => $workCenter_r2g->id,
                'opc_server_id' => $opcServer->id,
                'tag' => 'ns=2;s=IAV T6.Robot 1',
                'opc_tag_type_id' => $tagTypes['Downtime'],
                'downtime_category' => 'Robot #1',
            ],

        ];



        $opcTags = [];
        foreach ($tags as $tag) {


            $downtime = null;
            if ($tag['opc_tag_type_id'] == $tagTypes['Downtime']) {
                /** @var \App\Models\Downtime $downtime */
                $downtime = $plant->onPlantDb()->downtimes()->where('category', '=', $tag['downtime_category'])->first();
            }
            unset($tag['downtime_category']);
            $opcTag = new OpcTag($tag);
            $opcTag->setConnection($plantConnection)->save();
            $opcTag->activateTag();

            if (!$downtime)
                continue;

            
            //link downtime tag
            if ($tag['work_center_id'] == $workCenter_r2a->id)
                $workCenter_r2a->downtimes()->attach($downtime->id, ['opc_tag_id' => $opcTag->id]);
            else if ($tag['work_center_id'] == $workCenter_r2g->id)
                $workCenter_r2g->downtimes()->attach($downtime->id, ['opc_tag_id' => $opcTag->id]);
        }
    }
}
