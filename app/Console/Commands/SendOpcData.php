<?php

namespace App\Console\Commands;

use App\Jobs\SendToOpc;
use App\Models\OpcServer;
use App\Models\OpcTag;
use App\Models\Plant;
use App\Models\WorkCenter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SendOpcData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:opc_data {plant_uid} {work_center_uid} {opc_tag_type} {value} {line_no?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Query OPC Tag list from server';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $plantUid = $this->argument('plant_uid');
        $workCenterUid = $this->argument('work_center_uid');
        // $opcTagId = $this->argument('opc_tag_id');
        $opcTagType = $this->argument('opc_tag_type');
        $value = $this->argument('value');
        $line_no = $this->argument('line_no');


        $plant = Plant::where('uid', $plantUid)->first();
        if (!$plant)
            return;

        //$plant->loadAppDatabase();


        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $plant->onPlantDb()->workCenters()->where(WorkCenter::TABLE_NAME . '.uid', $workCenterUid)->first();
        if (!$workCenter) {
            $this->warn("Workcenter Not Found!");
            return 0;
        }

        $workCenter->sendToOpc($opcTagType, $value, $line_no);

        // if (!$opcTagId) {
        //     //print all opc tag;
        //     $opcTags = $workCenter->opcTags()->get();
        //     $this->warn("OPC Tag List For " . $workCenter->uid);
        //     foreach ($opcTags as $opcTag) {
        //         $this->info($opcTag->id . ': ' . $opcTag->tag);
        //     }
        //     return 0;
        // }

        // /** @var \App\Models\OpcTag $opcTag */
        // $opcTag = $workCenter->opcTags()->where(OpcTag::TABLE_NAME . '.id', '=', $opcTagId)->first();

        // if (!$opcTag) {
        //     $this->warn("Opc Tag Not Found!");
        //     return 0;
        // }


        // $opcServer = $plant->onMainDb()->opcServers()->where(OpcServer::TABLE_NAME . '.id', '=', $opcTag->opc_server_id)->first();

        // if (!$opcServer) {
        //     $this->warn("Opc Server Not Found!");
        //     return 0;
        // }

        // if (!$value) {
        //     $this->warn("Value not defined");
        //     return 0;
        // }
        // dispatch(new sendToOpc($workCenter, $opcTag, $value));


        // return 0;
    }
}
