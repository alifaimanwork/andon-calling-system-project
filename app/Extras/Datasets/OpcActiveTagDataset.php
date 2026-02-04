<?php

declare(strict_types=1);

namespace App\Extras\Datasets;

use App\Extras\Datasets\Traits\DatatableTrait;
use App\Extras\Datasets\Traits\QueryBuilderTrait;
use App\Models\Company;
use App\Models\OpcActiveTag;
use App\Models\OpcServer;
use App\Models\Plant;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class OpcActiveTagDataset
{
    use QueryBuilderTrait;
    use DatatableTrait;

    const TABLE_NAME = OpcActiveTag::TABLE_NAME;
    protected $table = self::TABLE_NAME;

    protected $datatableColumns = [
        'id' => [self::TABLE_NAME . '.id', '='],
        'plant_id' => [self::TABLE_NAME . '.plant_id', '='],
        'plant_name' => [Plant::TABLE_NAME . '.name', null, [Plant::TABLE_NAME, 'left']],
        'plant_uid' => [Plant::TABLE_NAME . '.uid', null, [Plant::TABLE_NAME, 'left']],
        'company_id' => [Plant::TABLE_NAME . '.company_id', null, [Plant::TABLE_NAME, 'left']],
        'company_name' => [Company::TABLE_NAME . '.name', null, [Company::TABLE_NAME, 'left']],
        'tag' => [self::TABLE_NAME . '.tag', null],
        'data_type' => [self::TABLE_NAME . '.data_type', null],
        'opc_server_id' => [self::TABLE_NAME . '.opc_server_id', null],
        'opc_server_name' => [OpcServer::TABLE_NAME . '.name', null, [OpcServer::TABLE_NAME, 'left']],
        'state' => [self::TABLE_NAME . '.state', null],
    ];

    private function applyFilters(Builder &$query): self
    {
        if (isset($this->filters['assigned'])) {
            if ($this->filters['assigned'])
                $query->whereNotNull($this->table . '.plant_id');
            else
                $query->whereNull($this->table . '.plant_id');
        }
        if (isset($this->filters['plant_id'])) {
            $query->where($this->table . '.plant_id', $this->filters['plant_id']);
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
            case Company::TABLE_NAME:
                if (!$this->joined($query, Plant::TABLE_NAME))
                    $query->join(Plant::TABLE_NAME, $this->table . '.plant_id', '=', Plant::TABLE_NAME . '.id', $joinType);
                if (!$this->joined($query, Company::TABLE_NAME))
                    $query->join(Company::TABLE_NAME, Plant::TABLE_NAME  . '.company_id', '=', Company::TABLE_NAME . '.id', $joinType);
                break;
            case OpcServer::TABLE_NAME:
                if (!$this->joined($query, OpcServer::TABLE_NAME))
                    $query->join(OpcServer::TABLE_NAME, $this->table . '.opc_server_id', '=', OpcServer::TABLE_NAME . '.id', $joinType);
                break;
        }
        return $this;
    }
    public function getCount(): int
    {
        return $this->getQuery()->count();
    }
}
