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

class ForceOpcResync implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $opcServerId;

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
    public function __construct(OpcServer $opcServer)
    {
        $this->opcServerId = $opcServer->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /** @var \App\Models\OpcServer $opcServer */
        $opcServer = OpcServer::find($this->opcServerId);

        if (!$opcServer)
            return;

        Log::info('ForceOpcResync:[' . $opcServer->id . '] JOB STARTED');
        $micro = microtime(true);
        $return_code = "";

        $responded = false;
        try {
            $client = new Client();
            $response = $client->post($opcServer->adapter_hostname . ':' . $opcServer->adapter_port, [
                'json' => [
                    'function' => 'force_resync',
                    'data' => [
                        'opc_server_id' => $opcServer->id,
                    ]
                ]
            ]);

            $return_code = $response->getBody();
            $responded = true;
        } catch (\Exception $e) {
            Log::error('Error sending command:' . $e->getMessage());
        }
        if (!$responded) {
            Log::error('Restart opc-adapter');
            pclose(popen('START ..\venv\Scripts\python.exe ..\opc-adapter\opcadapter', 'r'));
        }

        Log::info("ForceOpcResync[" . $return_code . '] JOB ENDED ' . (microtime(true) - $micro));
    }
}
