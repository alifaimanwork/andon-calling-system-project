<?php

declare(strict_types=1);

namespace App\Extras\Datasets;

use App\Extras\Datasets\Traits\DatatableTrait;
use App\Extras\Datasets\Traits\QueryBuilderTrait;
use App\Models\Company;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CompanyDataset
{
    use QueryBuilderTrait;
    use DatatableTrait;

    const TABLE_NAME = Company::TABLE_NAME;
    protected $table = self::TABLE_NAME;



    protected $datatableColumns = [
        'id' => [self::TABLE_NAME . '.id', '='],
        'name' => [self::TABLE_NAME . '.name', null],
        'code' => [self::TABLE_NAME . '.code', null],
        'logo' => [self::TABLE_NAME . '.logo', null]
    ];

    private function applyFilters(Builder &$query): self
    {
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
        return $this;
    }
    public function getCount(): int
    {
        return $this->getQuery()->count();
    }
}
