<?php

namespace App\Console\Commands;

use App\Models\Plant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class MigrateAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate_all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate All DB';

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
        if (!$this->confirm("Migrate all database?"))
            return 0;

        $this->warn("Run migration (Main Database)");
        $this->runCommand("migrate", ['--force' => null], $this->getOutput());

        $plants = Plant::get();

        /** @var \App\Models\Plant $plant */
        foreach ($plants as $plant) {
            $plant->loadAppDatabase();
            $dbConfig = json_decode($plant->database_configurations);
            if (!$dbConfig)
                continue;
            $this->warn("Run migration (Plant Database: " . $plant->uid . ")");
            $plant->migrateAppDatabase($this->getOutput());
        }
        return 0;
    }
}
