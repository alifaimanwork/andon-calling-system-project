<?php

namespace Database\Seeders;

use App\Models\DashboardLayout;
use App\Models\Plant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DashboardLayoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $layouts = [
            [
                'name' => 'Type 1 (1 Lines)',
                'capacity' => 1,
                'preview_image' => 'screen_1.png',
                'layout_data' => json_encode([
                    'view' => 'screen-1'
                ]),
            ],
            [
                'name' => 'Type 2 (2 Lines)',
                'capacity' => 2,
                'preview_image' => 'screen_2.png',
                'layout_data' => json_encode([
                    'view' => 'screen-2'
                ]),
            ],
            [
                'name' => 'Type 3 (4 Lines)',
                'capacity' => 4,
                'preview_image' => 'screen-4.png',
                'layout_data' => json_encode([
                    'view' => 'screen-4'
                ]),
            ],
            [
                'name' => 'Type 4 (6 Lines)',
                'capacity' => 6,
                'preview_image' => 'screen-6.png',
                'layout_data' => json_encode([
                    'view' => 'screen-6'
                ]),
            ],
            [
                'name' => 'Type 5 (7 Lines)',
                'capacity' => 7,
                'preview_image' => 'screen-7.png',
                'layout_data' => json_encode([
                    'view' => 'screen-7'
                ]),
            ],
        ];


        //$plants = Plant::get();
        //foreach ($plants as $plant) {
            //$plant->loadAppDatabase();
            
            foreach ($layouts as $layout) {
                (new DashboardLayout($layout))->save();
            }
        //}
    }
}
