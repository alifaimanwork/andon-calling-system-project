<?php

namespace Database\Seeders;

use App\Models\DowntimeType;
use App\Models\Plant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DowntimeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rowDefs = [
            [
                'id' => 1,
                'name' => 'Machine / Tooling',
            ],
            [
                'id' => 2,
                'name' => 'Human Downtime',
            ]
        ];

        foreach ($rowDefs as $row) {
            (new DowntimeType($row))->save();
        }
    }
}
