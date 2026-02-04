<?php

declare(strict_types=1);

namespace App\Extras\Datasets;

use App\Extras\Datasets\Traits\DatatableTrait;
use App\Extras\Datasets\Traits\PlantDatabaseTrait;
use App\Extras\Datasets\Traits\QueryBuilderTrait;
use App\Models\Company;
use App\Models\OpcActiveTag;
use App\Models\OpcServer;
use App\Models\OpcTag;
use App\Models\OpcTagType;
use App\Models\Plant;
use App\Models\WorkCenter;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class OpcTagDataset
{
    use QueryBuilderTrait;
    use DatatableTrait;
    use PlantDatabaseTrait;

    const TABLE_NAME = OpcTag::TABLE_NAME;
    protected $table = self::TABLE_NAME;

    protected $datatableColumns = [
        'id' => [self::TABLE_NAME . '.id', '='],

        'plant_id' => [self::TABLE_NAME . '.plant_id', '='],
        'plant_name' => [Plant::TABLE_NAME . '.name', null, [Plant::TABLE_NAME, 'left']],
        'plant_uid' => [Plant::TABLE_NAME . '.uid', null, [Plant::TABLE_NAME, 'left']],

        'work_center_id' => [self::TABLE_NAME . '.work_center_id', null],
        'work_center_uid' => [WorkCenter::TABLE_NAME . '.uid', null, [WorkCenter::TABLE_NAME, 'left']],
        'work_center_name' => [WorkCenter::TABLE_NAME . '.name', null, [WorkCenter::TABLE_NAME, 'left']],

        'tag' => [self::TABLE_NAME . '.tag', null],
        'opc_server_id' => [self::TABLE_NAME . '.opc_server_id', null],

        'opc_tag_type_id' => [self::TABLE_NAME . '.opc_tag_type_id', null],
        'opc_tag_type_name' => [OpcTagType::TABLE_NAME . '.name', null, [OpcTagType::TABLE_NAME, 'left']],

        'info' => [self::TABLE_NAME . '.info', null]
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
            case WorkCenter::TABLE_NAME:
                if (!$this->joined($query, WorkCenter::TABLE_NAME))
                    $query->join(WorkCenter::TABLE_NAME, $this->table . '.work_center_id', '=', WorkCenter::TABLE_NAME . '.id', $joinType);
                break;
            case OpcTagType::TABLE_NAME:
                if (!$this->joined($query, OpcTagType::TABLE_NAME))
                    $query->join(OpcTagType::TABLE_NAME, $this->table . '.opc_tag_type_id', '=', OpcTagType::TABLE_NAME . '.id', $joinType);
                break;
        }
        return $this;
    }
    public function getCount(): int
    {
        return $this->getQuery()->count();
    }
}
