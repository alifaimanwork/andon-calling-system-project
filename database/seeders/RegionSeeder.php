<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regions = [
            [
                'name' => 'Malaysia',
                'flag' => 'my.png'
            ], [
                'name' => 'Thailand',
                'flag' => 'th.png'
            ], [
                'name' => 'Indonesia',
                'flag' => 'id.png'
            ], [
                'name' => 'India',
                'flag' => 'in.png'
            ]
        ];
        foreach ($regions as $region) {
            (new Region($region))->save();
        }
    }
}
