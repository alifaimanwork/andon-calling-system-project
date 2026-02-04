<?php

declare(strict_types=1);

namespace App\Extras\Datasets;

use App\Extras\Datasets\Traits\DatatableTrait;
use App\Extras\Datasets\Traits\PlantDatabaseTrait;
use App\Extras\Datasets\Traits\QueryBuilderTrait;
use App\Models\BreakSchedule;
use App\Models\Company;
use App\Models\DashboardLayout;
use App\Models\Factory;
use App\Models\Plant;
use App\Models\Region;
use App\Models\WorkCenter;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WorkCenterDataset
{
    use QueryBuilderTrait;
    use PlantDatabaseTrait;
    use DatatableTrait;

    const TABLE_NAME = WorkCenter::TABLE_NAME;
    protected $table = self::TABLE_NAME;

    //Column definition
    //Key: column name (client side)
    //Value: [<column_name>, <search pattern>,[ <table name>,<join method> ] *optional kalau join table]

    protected $datatableColumns = [
        'id' => [self::TABLE_NAME . '.id', '='],
        'uid' => [self::TABLE_NAME . '.uid', '='],
        'name' => [self::TABLE_NAME . '.name', null],
        'line_count' => [self::TABLE_NAME . '.production_line_count', '='],
        'enabled' => [self::TABLE_NAME . '.enabled', '='],
        'region_name' => [Region::TABLE_NAME . '.name', null, [Region::TABLE_NAME, 'left']],
        'company_name' => [Company::TABLE_NAME . '.name', null, [Company::TABLE_NAME, 'left']],
        'dashboard_layout_name' => [DashboardLayout::TABLE_NAME . '.name', null , [DashboardLayout::TABLE_NAME, 'left']],
        'break_schedule' => [BreakSchedule::TABLE_NAME . '.name', null, [BreakSchedule::TABLE_NAME, 'left']],
    ];

    private function applyFilters(Builder &$query): self
    {
        if (isset($this->filters['factory_id'])) {
            $query->where($this->table . '.factory_id', '=', $this->filters['factory_id']);
        }

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
                //...
            case Factory::TABLE_NAME:
                if (!$this->joined($query, Factory::TABLE_NAME))
                    $query->join(Factory::TABLE_NAME, $this->table . '.factory_id', '=', Factory::TABLE_NAME . '.id', $joinType);
                break;
            case Plant::TABLE_NAME:
                if (!$this->joined($query, Factory::TABLE_NAME))
                    $this->applyJoinTable($query, Factory::TABLE_NAME, 'left');
                if (!$this->joined($query, Plant::TABLE_NAME))
                    $query->join(Plant::TABLE_NAME, Factory::TABLE_NAME . '.plant_id', '=', Plant::TABLE_NAME . '.id', $joinType);
                break;
            case Region::TABLE_NAME:
                if (!$this->joined($query, Plant::TABLE_NAME))
                    $this->applyJoinTable($query, Plant::TABLE_NAME, 'left');
                if (!$this->joined($query, Region::TABLE_NAME))
                    $query->join(Region::TABLE_NAME, Plant::TABLE_NAME . '.region_id', '=', Region::TABLE_NAME . '.id', $joinType);
                break;
            case Company::TABLE_NAME:
                if (!$this->joined($query, Plant::TABLE_NAME))
                    $this->applyJoinTable($query, Plant::TABLE_NAME, 'left');
                if (!$this->joined($query, Company::TABLE_NAME))
                    $query->join(Company::TABLE_NAME, Plant::TABLE_NAME . '.company_id', '=', Company::TABLE_NAME . '.id', $joinType);
                break;
            case DashboardLayout::TABLE_NAME:
                if (!$this->joined($query, DashboardLayout::TABLE_NAME))
                    $query->join(DashboardLayout::TABLE_NAME, $this->table . '.dashboard_layout_id', '=', DashboardLayout::TABLE_NAME . '.id', $joinType);
                break;
            case BreakSchedule::TABLE_NAME:
                if (!$this->joined($query, BreakSchedule::TABLE_NAME))
                    $query->join(BreakSchedule::TABLE_NAME, $this->table . '.break_schedule_id', '=', BreakSchedule::TABLE_NAME . '.id', $joinType);
                break;
        }

        return $this;
    }
    public function getCount(): int
    {
        return $this->getQuery()->count();
    }
}
