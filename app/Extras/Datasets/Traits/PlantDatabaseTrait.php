<?php

declare(strict_types=1);

namespace App\Extras\Datasets\Traits;

use App\Models\Plant;
use Illuminate\Database\Query\Builder;

trait PlantDatabaseTrait
{
    protected $plant = null;
    public function setPlant(Plant $plant, $filterByPlant = true): self
    {
        $this->plant = $plant;
        $plant->loadAppDatabase();
        $this->connection = $plant->getPlantConnection();

        if ($filterByPlant)
            $this->filters['plant_id'] = $plant->id;

        return $this;
    }
}
