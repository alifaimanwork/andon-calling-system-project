<?php

declare(strict_types=1);

namespace App\Extras\Traits;

use App\Models\Plant;
use App\Models\WorkCenter;

trait PlantTrait
{
    public function getPlant($plantUid, $associative = true)
    {
        /** @var \App\Models\Plant $plant */
        $plant = Plant::where('uid', $plantUid)->first();
        if (!$plant)
            return null;

        $plant->setActivePlant()->loadAppDatabase();

        $data = [
            'plant' => $plant
        ];
        return $associative ? $data : (object)$data;
    }
}
