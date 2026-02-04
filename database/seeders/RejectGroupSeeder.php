<?php

namespace Database\Seeders;

use App\Models\RejectGroup;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RejectGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //create reject group - setting, material, process
        $rejectGroups = [
            [
                'id' => 1,
                'name' => 'Setting',
            ], [
                'id' => 2,
                'name' => 'Material',
            ], [
                'id' => 3,
                'name' => 'Process',
            ]
        ];

        foreach ($rejectGroups as $rejectGroup) {
            (new RejectGroup($rejectGroup))->save();
        }
    }
}
