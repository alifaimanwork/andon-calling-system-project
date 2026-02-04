<?php

namespace App\Console\Commands;

use App\Models\Plant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class MigrateAllFresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate_all_fresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate Fresh All DB & Seed';

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
            $this->warn("Application in production. Command disabled!");
            return 0;
        }

        if (!$this->confirm("Migrate fresh --seed all database?"))
            return 0;

        $this->warn("Run migration");
        $this->runCommand("migrate", ['--force' => null], $this->getOutput());

        $plants = Plant::get();
        foreach ($plants as $plant) {
            $plant->loadAppDatabase();
            $dbConfig = json_decode($plant->database_configurations);
            if (!$dbConfig)
                continue;
            $dbName = $dbConfig->database;

            $rows = DB::connection($plant->getPlantConnection())
                ->select('SELECT table_name FROM information_schema.tables WHERE table_schema = ? ', [$dbName]);
            $this->info("Drop tables from " . $dbName);
            foreach ($rows as $row) {
                $tableName = $row->table_name;
                $this->info("Drop table " . $tableName);
                DB::connection($plant->getPlantConnection())
                    ->select('DROP TABLE IF EXISTS ' . $tableName);
            }
        }

        $this->warn("Run migration:fresh");
        $this->runCommand("migrate:fresh", ['--force' => null], $this->getOutput());

        $this->warn("Run db:seed");
        $this->runCommand("db:seed", ['--force' => null], $this->getOutput());
        return 0;
    }
}
