<?php

namespace App\Console\Commands;

use App\Models\OpcTagType;
use App\Models\Plant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class AppUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app_update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update current app to latest';

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
        if (!$this->confirm("Run update script?"))
            return 0;

        $this->update_202208111229_opcTagTypes();
        $this->info('Completed');
        return 0;
    }

    private function update_202208111229_opcTagTypes()
    {
        $this->warn('Updating OPC Tag Types...');
        $plants =  Plant::get();
        $tagTypes = [
            [
                'id' => 1,
                'name' => 'Die Change'
            ],
            [
                'id' => 2,
                'name' => 'Break'
            ],
            [
                'id' => 3,
                'name' => 'Part Number'
            ],
            [
                'id' => 4,
                'name' => 'Counter'
            ],
            [
                'id' => 5,
                'name' => 'Downtime'
            ],
            [
                'id' => 6,
                'name' => 'Human Downtime'
            ],
            [
                'id' => 7,
                'name' => 'On Production'
            ],
        ];
        $connections = [];
        $connections[] = null;
        /** @var \App\Models\Plant $plant */
        foreach ($plants as $plant) {
            $connections[] = $plant->onPlantDb()->getPlantConnection();
        }
        foreach ($connections as $connection) {
            foreach ($tagTypes as $tagType) {
                $exist = OpcTagType::on($connection)->find($tagType['id']);
                if ($exist)
                    continue;

                $newTag = new OpcTagType();
                $newTag->id = $tagType['id'];
                $newTag->name = $tagType['name'];
                $newTag->setConnection($connection)->save();
            }
        }
    }
}
