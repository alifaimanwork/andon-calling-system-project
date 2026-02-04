<?php

declare(strict_types=1);

namespace App\Extras\Datasets;

use App\Extras\Datasets\Traits\DatatableTrait;
use App\Extras\Datasets\Traits\PlantDatabaseTrait;
use App\Extras\Datasets\Traits\QueryBuilderTrait;
use App\Models\DowntimeReason;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DowntimeReasonDataset
{
    use QueryBuilderTrait;
    use PlantDatabaseTrait;
    use DatatableTrait;

    const TABLE_NAME = DowntimeReason::TABLE_NAME;
    protected $table = self::TABLE_NAME;

    //Column definition
    //Key: column name (client side)
    //Value: [<column_name>, <search pattern>,[ <table name>,<join method> ] *optional kalau join table]

    protected $datatableColumns = [
        'id' => [self::TABLE_NAME . '.id', '='],
        'downtime_id' => [self::TABLE_NAME . '.downtime_id', '='],
        'reason' => [self::TABLE_NAME . '.reason', null],
        'user_input' => [self::TABLE_NAME . '.enable_user_input', '='],
        'enabled' => [self::TABLE_NAME . '.enabled', '='],
    ];

    private function applyFilters(Builder &$query): self
    {
        if (isset($this->filters['reason'])) {
            $query->where($this->table . '.reason', '=', $this->filters['reason']);
        }
        if (isset($this->filters['downtime_id'])) {
            $query->where($this->table . '.downtime_id', '=', $this->filters['downtime_id']);
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
                //...
        }

        return $this;
    }
    public function getCount(): int
    {
        return $this->getQuery()->count();
    }
}
