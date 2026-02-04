<?php

namespace App\Console\Commands;

use App\Models\Plant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class DevWsStart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:ws_start {worker?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start websocket, queue worker, schedule work & opc adapter';

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
        if (App::environment('production')) {
            if (!$this->confirm("Application in production. Command only intended for development. Continue?"))
                return 0;
        }
        $worker = $this->argument('worker');
        if (!$worker || !is_numeric($worker))
            $worker = 1;
        if (PHP_OS_FAMILY == 'Windows') {



            if ($this->confirm("Start OPC Adapter?")) {
                $this->info("Kill previous opc adapter");
                pclose(popen("tskill python", 'r'));

                $this->info("Starting OPC Adapter...");
                pclose(popen('START ..\venv\Scripts\python.exe ..\opc-adapter\opcadapter', 'r'));
            }

            $this->info("Send stop work signal...");
            pclose(popen("START php artisan queue:restart", 'r'));
            sleep(5);
            $this->info("Starting websocket server...");
            pclose(popen("START php artisan websocket:serve", 'r'));

            $this->info("Start workers...");
            for ($n = 0; $n < $worker; $n++) {
                pclose(popen("START php artisan queue:work", 'r'));
            }

            pclose(popen("START php artisan queue:work --queue=low --timeout=120", 'r'));

            // pclose(popen("START php artisan schedule:work", 'r'));
        } else {
            $this->info("No dev script for current OS: " . PHP_OS_FAMILY);
        }


        return 0;
    }
}
