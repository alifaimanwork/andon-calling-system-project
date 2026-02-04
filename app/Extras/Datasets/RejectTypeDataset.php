<?php

declare(strict_types=1);

namespace App\Extras\Datasets;

use App\Extras\Datasets\Traits\DatatableTrait;
use App\Extras\Datasets\Traits\PlantDatabaseTrait;
use App\Extras\Datasets\Traits\QueryBuilderTrait;
use App\Models\RejectType;
use App\Models\RejectGroup;
use App\Models\Plant;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RejectTypeDataset
{
    use QueryBuilderTrait;
    use PlantDatabaseTrait;
    use DatatableTrait;

    const TABLE_NAME = RejectType::TABLE_NAME;
    protected $table = self::TABLE_NAME;

    protected $datatableColumns = [
        'id' => [self::TABLE_NAME . '.id', '='],
        'plant_id' => [self::TABLE_NAME . '.plant_id', '='],
        'plant_name' => [Plant::TABLE_NAME . '.name', '=', [Plant::TABLE_NAME, 'left']],
        'reject_type' => [self::TABLE_NAME . '.name', null],
        'enabled' => [self::TABLE_NAME . '.enabled', '='],
        'locked' => [self::TABLE_NAME . '.locked', '='],
        'reject_group_id' => [self::TABLE_NAME . '.reject_group_id', '=', [RejectGroup::TABLE_NAME, 'left']],
        'reject_group_name' => [RejectGroup::TABLE_NAME . '.name', '=', [RejectGroup::TABLE_NAME, 'left']]
    ];

    private function applyFilters(Builder &$query): self
    {
        if (isset($this->filters['plant_id'])) {
            $query->where($this->table . '.plant_id', '=', $this->filters['plant_id']);
        }
        if (isset($this->filters['reject_group_id'])) {
            $query->where($this->table . '.reject_group_id', '=', $this->filters['reject_group_id']);
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
            case RejectGroup::TABLE_NAME:
                if (!$this->joined($query, RejectGroup::TABLE_NAME))
                    $query->join(RejectGroup::TABLE_NAME, $this->table . '.reject_group_id', '=', RejectGroup::TABLE_NAME . '.id', $joinType);
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
