<?php

declare(strict_types=1);

namespace App\Extras\Datasets;

use App\Extras\Datasets\Traits\DatatableTrait;
use App\Extras\Datasets\Traits\QueryBuilderTrait;
use App\Models\Company;
use App\Models\Plant;
use App\Models\Region;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PlantDataset
{
    use QueryBuilderTrait;
    use DatatableTrait;

    const TABLE_NAME = Plant::TABLE_NAME;
    protected $table = self::TABLE_NAME;

    protected $datatableColumns = [
        'id' => [self::TABLE_NAME . '.id', '='],
        'name' => [self::TABLE_NAME . '.name', null],
        'uid' => [self::TABLE_NAME . '.uid', null],
        'sap_id' => [self::TABLE_NAME . '.sap_id', null],
        'time_zone' => [self::TABLE_NAME . '.time_zone', null],
        'total_employee' => [self::TABLE_NAME . '.total_employee', null],
        'total_production_line' => [self::TABLE_NAME . '.total_production_line', null],
        'company_name' => [Company::TABLE_NAME . '.name', null, [Company::TABLE_NAME, 'left']],
        'region_name' => [Region::TABLE_NAME . '.name', null, [Region::TABLE_NAME, 'left']],
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
        switch ($table) {
            case Company::TABLE_NAME:
                if (!$this->joined($query, Company::TABLE_NAME))
                    $query->join(Company::TABLE_NAME, $this->table . '.company_id', '=', Company::TABLE_NAME . '.id', $joinType);
                break;
            case Region::TABLE_NAME:
                if (!$this->joined($query, Region::TABLE_NAME))
                    $query->join(Region::TABLE_NAME, $this->table . '.region_id', '=', Region::TABLE_NAME . '.id', $joinType);
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
