<?php

namespace Database\Seeders;

use App\Models\OpcTagType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OpcTagTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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
        foreach ($tagTypes as $tagType) {
            (new OpcTagType($tagType))->save();
        }
    }
}
