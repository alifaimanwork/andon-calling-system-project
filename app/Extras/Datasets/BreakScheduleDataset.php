<?php

declare(strict_types=1);

namespace App\Extras\Datasets;

use App\Extras\Datasets\Traits\DatatableTrait;
use App\Extras\Datasets\Traits\PlantDatabaseTrait;
use App\Extras\Datasets\Traits\QueryBuilderTrait;
use App\Models\BreakSchedule;
use App\Models\Plant;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BreakScheduleDataset
{
    use QueryBuilderTrait;
    use PlantDatabaseTrait;
    use DatatableTrait;

    const TABLE_NAME = BreakSchedule::TABLE_NAME;
    protected $table = self::TABLE_NAME;

    protected $datatableColumns = [
        'id' => [self::TABLE_NAME . '.id', '='],
        'plant_id' => [self::TABLE_NAME . '.plant_id', '='],
        'enabled' => [self::TABLE_NAME . '.enabled', null],
        'name' => [self::TABLE_NAME . '.name', null],
    ];

    private function applyFilters(Builder &$query): self
    {
        if (isset($this->filters['plant_id'])) {
            $query->where($this->table . '.plant_id', '=', $this->filters['plant_id']);
        }
        if (isset($this->filters['enabled'])) {
            $query->where($this->table . '.enabled', '=', $this->filters['enabled']);
        }
        if (isset($this->filters['name'])) {
            $query->where($this->table . '.name', '=', $this->filters['name']);
        }

        return $this;
    }
    public function get(): Collection
    {
        return $this->getQuery()->get();
    }
    public function getQuery(): Builder
    {
        if ($this->connection ?? false)
            $query = DB::connection($this->connection)->table($this->table);
        else
            $query = DB::table($this->table);



        //apply join table
        foreach ($this->query_join_tables as $table => $join_table) {
            $this->applyJoinTable($query, $join_table->table, $join_table->join_type);
        }

        //apply select
        $this->applySelect($query)
            ->applyGroupBy($query)
            ->applyFilters($query);

        return $query;
    }
    private function applyJoinTable(Builder &$query, $table, $joinType = 'inner'): self
    {
        switch ($table) {
            case Plant::TABLE_NAME:
                if (!$this->joined($query, Plant::TABLE_NAME))
                    $query->join(Plant::TABLE_NAME, $this->table . '.plant_id', '=', Plant::TABLE_NAME . '.id', $joinType);
                break;
                //...
        }

        return $this;
    }
    public function getCount(): int
    {
        return $this->getQuery()->count();
    }
}
