<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\OpcServer;
use App\Models\Plant;
use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\ConsoleOutput;

class PlantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $config = [
            'host' => env('TESTPLANT_DB_HOST', env('DB_HOST', '127.0.0.1')),
            'port' => env('TESTPLANT_DB_PORT', env('DB_PORT', '3306')),
            'database' => env('TESTPLANT_DB_DATABASE', env('DB_DATABASE', 'ipos')),
            'username' => env('TESTPLANT_DB_USERNAME', env('DB_USERNAME', 'root')),
            'password' => env('TESTPLANT_DB_PASSWORD', env('DB_PASSWORD', '')),
        ];
        $regions = Region::pluck('id', 'name')->toArray();
        $companies = Company::pluck('id', 'code')->toArray();

        $plants = [
        [
            'region_id' => $regions['Thailand'],
            'company_id' => $companies['IAV'],
            'uid' => 'iav-rayong',
            'name' => 'IAV - RAYONG PLANT',
            'sap_id' => 'IAV_RAYONG',
            'time_zone' => 'Asia/Bangkok',
            'total_employee' => 100,
            'total_production_line' => 24,
            'database_configurations' => json_encode($config)
        ], [
            'region_id' => $regions['Thailand'],
            'company_id' => $companies['IAV'],
            'uid' => 'iav-ayutthaya',
            'name' => 'IAV - AYUTTHAYA PLANT',
            'sap_id' => 'IAV_AYUTTHAYA',
            'time_zone' => 'Asia/Bangkok',
            'total_employee' => 120,
            'total_production_line' => 28,
            'database_configurations' => json_encode($config)
        ], [
            'region_id' => $regions['Thailand'],
            'company_id' => $companies['FINE'],
            'uid' => 'fine-component',
            'name' => 'FINE COMPONENT',
            'sap_id' => 'FINE_COMPONENT',
            'time_zone' => 'Asia/Bangkok',
            'total_employee' => 80,
            'total_production_line' => 18,
            'database_configurations' => json_encode($config)
        ], [
            'region_id' => $regions['Malaysia'],
            'company_id' => $companies['SMI'],
            'uid' => 'smi-beranang',
            'name' => 'SMI BERANANG',
            'sap_id' => 'SMI_BERANANG',
            'time_zone' => 'Asia/Kuala_Lumpur',
            'total_employee' => 77,
            'total_production_line' => 111,
            'database_configurations' => json_encode($config)
        ]];

        foreach ($plants as $plant) {
            //dd($plant);
            $newPlant = new Plant($plant);

            //load default layout
            $layoutPath = 'resources' . DIRECTORY_SEPARATOR . 'default_plant_layout' . DIRECTORY_SEPARATOR . $newPlant->uid . '.svg';
            if (file_exists($layoutPath)) {
                $newPlant->overview_layout_data = file_get_contents($layoutPath);
            }

            $newPlant->saveQuietly();  //save without trigger observer events

            $newPlant->loadAppDatabase();
            $consoleOutput = new ConsoleOutput();
            $this->command->info("Running Migration on Plant Database");
            
            $newPlant->migrateAppDatabase($consoleOutput);

            $opcServers = OpcServer::get();
            foreach ($opcServers as $opcServer) {
                $newPlant->setConnection(null)->opcServers()->attach($opcServer);
            }

            $newPlant->syncAllData();
        }
    }
}
