<?php

namespace Database\Seeders;

use App\Models\Plant;
use App\Models\RejectType;
use App\Models\RejectGroup;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RejectTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $rejectTypes = [
            [
                'reject_group_id' => 1,
                'enabled' => 1,
                'name' => 'MAINTENANCE',
                'tag' => 'maintenance',
                'locked' => 1,
            ], [
                'reject_group_id' => 1,
                'enabled' => 1,
                'name' => 'QUALITY',
                'tag' => 'quality',
                'locked' => 1,
            ], [
                'reject_group_id' => 2,
                'enabled' => 1,
                'name' => 'DEFORM',
            ], [
                'reject_group_id' => 2,
                'enabled' => 1,
                'name' => 'RUSTY',
            ], [
                'reject_group_id' => 2,
                'enabled' => 1,
                'name' => 'SCRATCH',
            ], [
                'reject_group_id' => 2,
                'enabled' => 1,
                'name' => 'BAR TWIST',
            ], [
                'reject_group_id' => 2,
                'enabled' => 1,
                'name' => 'OTHERS',
            ], [
                'reject_group_id' => 2,
                'enabled' => 1,
                'name' => 'REJECT 3.3',
            ], [
                'reject_group_id' => 3,
                'enabled' => 1,
                'name' => 'OVER CUTTING',
            ], [
                'reject_group_id' => 3,
                'enabled' => 1,
                'name' => 'PART DEFORM',
            ], [
                'reject_group_id' => 3,
                'enabled' => 1,
                'name' => 'MISS SETTING',
            ], [
                'reject_group_id' => 3,
                'enabled' => 1,
                'name' => 'SCRATCH',
            ], [
                'reject_group_id' => 3,
                'enabled' => 1,
                'name' => 'HUMP',
            ]
        ];

        //get all plant ids
        $plants = Plant::all();
        foreach ($plants as $plant) {
            $plant->loadAppDatabase();
            $rejectTypeObjects = [];
            foreach ($rejectTypes as $rejectType) {
                $rejectType['plant_id'] = $plant->id;
                $rejectTypeObjects[] = RejectType::on($plant->getPlantConnection())->create($rejectType);
            }

            $parts = $plant->onPlantDb()->parts()->get();
            /** @var \App\Models\Part $part */
            foreach ($parts as $part) {

                /** @var \App\Models\RejectType $rejectTypeObject */
                foreach ($rejectTypeObjects as $rejectTypeObject) {
                    $part->partRejectTypes()->attach($rejectTypeObject->id);
                }
            }
        }
    }
}
