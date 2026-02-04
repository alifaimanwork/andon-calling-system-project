<?php

namespace Database\Seeders;

use App\Models\Factory;
use App\Models\Plant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FactorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $factories = ['Factory 1', 'Factory 2', 'Factory 3'];

        $plant = Plant::where('uid', 'iav-rayong')->first();
        $plant->loadAppDatabase();

        foreach ($factories as $factoryName) {
            (new Factory([
                'plant_id' => $plant->id,
                'name' => $factoryName,
                'uid' => str_replace(' ', '-', strtolower($factoryName))
            ]))->setConnection($plant->getPlantConnection())->save();
        }

        $factoriesDua = ['SMI Factory 1'];

        $plantDua = Plant::where('uid', 'smi-beranang')->first();
        $plantDua->loadAppDatabase();

        foreach ($factoriesDua as $factoryNameDua) {
            (new Factory([
                'plant_id' => $plantDua->id,
                'name' => $factoryNameDua,
                'uid' => str_replace(' ', '-', strtolower($factoryNameDua))
            ]))->setConnection($plantDua->getPlantConnection())->save();
        }
    }
}
