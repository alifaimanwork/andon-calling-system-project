<?php

declare(strict_types=1);

namespace App\Extras\Traits;

use App\Models\Factory;
use App\Models\Plant;

trait FactoryTrait
{
    public function getPlantFactory($plantUid, $factoryUid = null): array | null
    {
        $plant = Plant::where('uid', $plantUid)->first();
        if (!$plant)
            return null;

        if (!is_null($factoryUid)) {
            $factory = $plant->onPlantDb()->factories()->where(Factory::TABLE_NAME . '.uid', $factoryUid)->first();
            if (!$factory)
                return null;
        } else
            $factory = null;

        $data = [
            'plant' => $plant,
            'factory' => $factory //nullable factory, null = show all
        ];
        return $data;
    }
}
