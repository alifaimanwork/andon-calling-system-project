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

class SignalOpcSettingUpdated implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 60;

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return '0';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('SignalOpcSettingUpdated[] JOB STARTED');
        $micro = microtime(true);

        try {
            $client = new Client();
            $response = $client->post(OpcServer::getOpcAdapterBaseUrl(), [
                'json' => [
                    'function' => 'new_tags_config_available',
                    'data' => []
                ]
            ]);

            $return_code = $response->getBody();
        } catch (\Exception $e) {
            Log::error('Error sending to OPC:' . $e->getMessage());
        }

        Log::info('SignalOpcSettingUpdated[' . $return_code . '] JOB ENDED ' . (microtime(true) - $micro));
    }
}
