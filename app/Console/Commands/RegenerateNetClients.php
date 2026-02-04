<?php

namespace App\Console\Commands;

use App\Models\MonitorClient;
use App\Models\Plant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class RegenerateNetClients extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'regenerate_net_clients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate all missing net client';

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
        if (!$this->confirm("Regenerate All Network Clients?"))
            return 0;

        $plants = Plant::get();

        /** @var \App\Models\Plant $plant */
        foreach ($plants as $plant) {
            $workCenters = $plant->onPlantDb()->workCenters()->get();

            /** @var \App\Models\WorkCenter $workCenter */
            foreach ($workCenters as $workCenter) {
                $dashboardClient = $workCenter->monitorClients()->where('client_type', MonitorClient::CLIENT_TYPE_DASHBOARD)->first();
                if (!$dashboardClient) {
                    $newToken = MonitorClient::generateNewUid($plant);
                    if (!$newToken) {
                        $this->warn("ERROR GENERATING NEW TOKEN!");
                        continue;
                    }
                    $dashboardClient = new MonitorClient();
                    $dashboardClient->plant_id = $plant->id;
                    $dashboardClient->target_id = $workCenter->id;
                    $dashboardClient->client_type = MonitorClient::CLIENT_TYPE_DASHBOARD;
                    $dashboardClient->uid = $newToken;
                    $dashboardClient->state = -1;
                    $dashboardClient->name = $workCenter->name . ' [DASHBOARD]';
                    $dashboardClient->setConnection($plant->getPlantConnection())->save();
                }


                $terminalClient = $workCenter->monitorClients()->where('client_type', MonitorClient::CLIENT_TYPE_TERMINAL)->first();
                if (!$terminalClient) {
                    $newToken = MonitorClient::generateNewUid($plant);
                    if (!$newToken) {
                        $this->warn("ERROR GENERATING NEW TOKEN!");
                        continue;
                    }
                    $terminalClient = new MonitorClient();
                    $terminalClient->plant_id = $plant->id;
                    $terminalClient->target_id = $workCenter->id;
                    $terminalClient->client_type = MonitorClient::CLIENT_TYPE_TERMINAL;
                    $terminalClient->uid = $newToken;
                    $terminalClient->state = -1;
                    $terminalClient->name = $workCenter->name . ' [TERMINAL]';
                    $terminalClient->setConnection($plant->getPlantConnection())->save();
                }
            }
        }
        return 0;
    }
}
