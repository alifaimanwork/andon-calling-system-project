<?php

declare(strict_types=1);

namespace App\Extras\Datasets;

use App\Extras\Datasets\Traits\DatatableTrait;
use App\Extras\Datasets\Traits\PlantDatabaseTrait;
use App\Extras\Datasets\Traits\QueryBuilderTrait;
use App\Models\Downtime;
use App\Models\DowntimeType;
use App\Models\Part;
use App\Models\Plant;
use App\Models\WorkCenter;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PartDataset
{
    use QueryBuilderTrait;
    use PlantDatabaseTrait;
    use DatatableTrait;

    const TABLE_NAME = Part::TABLE_NAME;
    protected $table = self::TABLE_NAME;

    //Column definition
    //Key: column name (client side)
    //Value: [<column_name>, <search pattern>,[ <table name>,<join method> ] *optional kalau join table]

    protected $datatableColumns = [
        'id' => [self::TABLE_NAME . '.id', '='],
        'plant_id' => [self::TABLE_NAME . '.plant_id', '='],
        'line_no' => [self::TABLE_NAME . '.line_no', '='],
        'part_no' => [self::TABLE_NAME . '.part_no', null],
        'name' => [self::TABLE_NAME . '.name', null],
        'setup_time' => [self::TABLE_NAME . '.setup_time', null],
        'cycle_time' => [self::TABLE_NAME . '.cycle_time', null],
        'packaging' => [self::TABLE_NAME . '.packaging', null],
        'reject_target' => [self::TABLE_NAME . '.reject_target', null],
        'side' => [self::TABLE_NAME . '.side', null],
        'enabled' => [self::TABLE_NAME . '.enabled', '='],
        'opc_part_id' => [self::TABLE_NAME . '.opc_part_id', null],
        'work_center_name' => [WorkCenter::TABLE_NAME . '.name', null, [WorkCenter::TABLE_NAME,'left']]
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
        //define cara join
        switch ($table) {
            case Plant::TABLE_NAME:
                if (!$this->joined($query, Plant::TABLE_NAME))
                    $query->join(Plant::TABLE_NAME, $this->table . '.plant_id', '=', Plant::TABLE_NAME . '.id', $joinType);
                break;
            case WorkCenter::TABLE_NAME:
                if (!$this->joined($query, WorkCenter::TABLE_NAME))
                    $query->join(WorkCenter::TABLE_NAME, $this->table . '.work_center_id', '=', WorkCenter::TABLE_NAME . '.id', $joinType);
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
