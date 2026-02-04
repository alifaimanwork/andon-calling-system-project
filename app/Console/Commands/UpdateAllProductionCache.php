<?php

namespace App\Console\Commands;

use App\Models\OpcActiveTag;
use App\Models\OpcServer;
use App\Models\OpcTag;
use App\Models\Plant;
use App\Models\Production;
use App\Models\WorkCenter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UpdateAllProductionCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'production:update_cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update runtime summary cache';

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

            $productions =  Production::on($plant->onPlantDb()->getPlantConnection())->get();

            $this->warn($plant->uid . ' [' . count($productions) . ']');
            /** @var \App\Models\Production $production */
            foreach ($productions as $production) {
                $production->updateRuntimeSummaryCache()->save();
            }
        }
        return 0;
    }
}
