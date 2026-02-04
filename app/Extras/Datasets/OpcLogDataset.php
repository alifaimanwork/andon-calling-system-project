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


class OpcLogDataset
{
    use QueryBuilderTrait;
    use DatatableTrait;

    const TABLE_NAME = 'opc_logs';
    protected $table = self::TABLE_NAME;

    protected $datatableColumns = [
        'id' => [self::TABLE_NAME . '.id', '='],
        'server_id' => [self::TABLE_NAME . '.server_id', '='],
        'server_name' => [OpcServer::TABLE_NAME . '.name', '=', [OpcServer::TABLE_NAME, 'left']],
        'tag' => [self::TABLE_NAME . '.tag', null],
        'value' => [self::TABLE_NAME . '.value', '='],
        'created_at' => [self::TABLE_NAME . '.created_at', null],
        '_from' => [self::TABLE_NAME . '.created_at', '>='],
        '_to' => [self::TABLE_NAME . '.created_at', '<='],
    ];

    private function applyFilters(Builder &$query): self
    {
        if (isset($this->filters['from'])) {
            $query->where($this->table . '.created_at', '>=', $this->filters['from']);
        }
        if (isset($this->filters['to'])) {
            $query->where($this->table . '.created_at', '<=', $this->filters['to']);
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
            case OpcServer::TABLE_NAME:
                if (!$this->joined($query, OpcServer::TABLE_NAME))
                    $query->join(OpcServer::TABLE_NAME, $this->table . '.server_id', '=', OpcServer::TABLE_NAME . '.id', $joinType);
                break;
        }
        return $this;
    }
    public function getCount(): int
    {
        return $this->getQuery()->count();
    }
}
