<?php

namespace Database\Seeders;

use App\Models\ShiftType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShiftTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shifTypes = [
            [
                'id' => 1,
                'name' => 'Day',
                'label' => 'D/S',
            ],
            [
                'id' => 2,
                'name' => 'Night',
                'label' => 'N/S',
            ],
        ];
        foreach ($shifTypes as $shiftType) {
            (new ShiftType($shiftType))->save();
        }
    }
}
