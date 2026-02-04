<?php

namespace Database\Seeders;

use App\Models\BreakSchedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            //Lookup Tables
            ShiftTypeSeeder::class,
            RejectGroupSeeder::class,
            DowntimeTypeSeeder::class,
            OpcTagTypeSeeder::class,

            OpcServerSeeder::class,
            RegionSeeder::class,
            CompanySeeder::class,
            DashboardLayoutSeeder::class,
            PlantSeeder::class,
            FactorySeeder::class,
            WorkCenterSeeder::class,
            PartSeeder::class,
            DowntimeSeeder::class,
            
            BreakScheduleSeeder::class,
            BreakTimeSeeder::class,

            DowntimeReasonSeeder::class,
            HumanDowntimeSeeder::class,

            RejectTypeSeeder::class,
            OpcTagSeeder::class,
            UserSeeder::class,

        ]);
    }
}
