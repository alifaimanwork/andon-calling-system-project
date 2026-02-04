<?php

namespace App\Console\Commands;

use App\Models\MonitorClient;
use App\Models\Plant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProbeNetworkTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule-task:net-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probe All Network Node';

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

        $plants = Plant::get();
        /** @var \App\Models\Plant $plant */
        foreach ($plants as $plant) {
            $plant->loadAppDatabase();
            $clients = MonitorClient::on($plant->getPlantConnection())->where('enabled', 1)->where('client_type', MonitorClient::CLIENT_TYPE_NETWORK_NODE)->get();
            /** @var \App\Models\MonitorClient $client */
            foreach ($clients as $client) {
                $client->probe()->save();
            }
        }
        return 0;
    }
}
