<?php

declare(strict_types=1);

namespace App\Extras\Datasets;

use App\Extras\Datasets\Traits\DatatableTrait;
use App\Extras\Datasets\Traits\PlantDatabaseTrait;
use App\Extras\Datasets\Traits\QueryBuilderTrait;
use App\Models\Downtime;
use App\Models\DowntimeType;
use App\Models\Plant;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DowntimeDataset
{
    use QueryBuilderTrait;
    use PlantDatabaseTrait;
    use DatatableTrait;

    const TABLE_NAME = Downtime::TABLE_NAME;
    protected $table = self::TABLE_NAME;

    protected $datatableColumns = [
        'id' => [self::TABLE_NAME . '.id', '='],
        'plant_id' => [self::TABLE_NAME . '.plant_id', '='],
        'plant_name' => [Plant::TABLE_NAME . '.name', '=', [Plant::TABLE_NAME, 'left']],
        'downtime_type_id' => [DowntimeType::TABLE_NAME . '.id', '=', [DowntimeType::TABLE_NAME, 'left']],
        'downtime_type_name' => [DowntimeType::TABLE_NAME . '.name', '=', [DowntimeType::TABLE_NAME, 'left']],
        'category' => [self::TABLE_NAME . '.category', null],
        'enabled' => [self::TABLE_NAME . '.enabled', '=']
    ];

    private function applyFilters(Builder &$query): self
    {
        if (isset($this->filters['plant_id'])) {
            $query->where($this->table . '.plant_id', '=', $this->filters['plant_id']);
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
            case DowntimeType::TABLE_NAME:
                if (!$this->joined($query, DowntimeType::TABLE_NAME))
                    $query->join(DowntimeType::TABLE_NAME, $this->table . '.downtime_type_id', '=', DowntimeType::TABLE_NAME . '.id', $joinType);
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
