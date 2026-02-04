<?php

namespace App\Jobs;

use App\Models\Plant;
use App\Models\OpcTag;
use GuzzleHttp\Client;
use App\Models\Downtime;
use App\Models\OpcServer;
use App\Models\Production;
use App\Models\WorkCenter;
use App\Models\DowntimeEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendToOpc implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $plantId;
    public $workCenterId;
    public $opcTagId;
    public $sendValue;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(WorkCenter $workCenter, OpcTag $opcTag, $value)
    {
        /** @var \App\Models\ */
        $plant = $workCenter->plant;

        $this->plantId = $plant->id;
        $this->workCenterId = $workCenter->id;
        $this->opcTagId = $opcTag->id;
        $this->sendValue = $value;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {


        /** @var \App\Models\Plant $plant */
        $plant = Plant::find($this->plantId);
        if (!$plant)
            return;

        $plant->loadAppDatabase();

        /** @var \App\Models\OpcTag $opcTag */
        $opcTag = $plant->onPlantDb()->opcTags()->where(OpcTag::TABLE_NAME . '.id', '=', $this->opcTagId)->first();

        if (!$opcTag)
            return;

        /** @var \App\Models\OpcServer $opcServer */
        $opcServer = $plant->onMainDb()->opcServers()->where(OpcServer::TABLE_NAME . '.id', '=', $opcTag->opc_server_id)->first();

        if (!$opcServer)
            return;

        // SET AT WORK CENTER DURING DISPATCH
        // //set value to active opc tag
        // /** @var \App\Models\OpcActiveTag $opcActiveTag */
        // $opcActiveTag = $opcServer->opcActiveTags()->where('tag', $opcTag->tag)->first();
        // if ($opcActiveTag) {
        //     $opcActiveTag->set_value = $this->sendValue;
        //     $opcActiveTag->save();
        // }


        Log::info('SendToOpc:[' . $opcTag->id . '-' . $opcServer->id . ' - ' . $opcTag->tag . ' - ' . $this->sendValue . '] JOB STARTED');
        $micro = microtime(true);
        $return_code = "";

        try {
            $client = new Client();
            $response = $client->post($opcServer->adapter_hostname . ':' . $opcServer->adapter_port, [
                'json' => [
                    'function' => 'send_data',
                    'data' => [
                        'opc_server_id' => $opcTag->opc_server_id,
                        'tag' => $opcTag->tag,
                        'value' => intval($this->sendValue)
                    ]
                ]
            ]);

            $return_code = $response->getBody();
        } catch (\Exception $e) {
            Log::error('Error sending to OPC:' . $opcTag->id . '-' . $opcServer->id . ' - ' . $opcTag->tag . ' - ' . $this->sendValue . ' - ' . $e->getMessage());
        }

        Log::info("SendToOpc[" . $return_code . '] JOB ENDED ' . (microtime(true) - $micro));
    }
}
