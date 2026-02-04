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
use App\Models\MonitorClient;
use App\Models\Plant;
use App\Models\Region;
use App\Models\WorkCenter;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MonitorClientDataset
{
    use QueryBuilderTrait;
    use PlantDatabaseTrait;
    use DatatableTrait;

    const TABLE_NAME = MonitorClient::TABLE_NAME;
    protected $table = self::TABLE_NAME;

    //Column definition
    //Key: column name (client side)
    //Value: [<column_name>, <search pattern>,[ <table name>,<join method> ] *optional kalau join table]

    protected $datatableColumns = [
        'id' => [self::TABLE_NAME . '.id', '='],
        'plant_id' => [self::TABLE_NAME . '.plant_id', '='],
        'plant_uid' => [Plant::TABLE_NAME . '.uid', null, [Plant::TABLE_NAME, 'left']],
        'target_id' => [self::TABLE_NAME . '.target_id', '='],
        'target_uid' => [WorkCenter::TABLE_NAME . '.uid', '=', [WorkCenter::TABLE_NAME, 'left']],
        'client_type' => [self::TABLE_NAME . '.client_type', '='],
        'name' => [self::TABLE_NAME . '.name', null],
        'client_info' => [self::TABLE_NAME . '.client_info', null],
        'uid' => [self::TABLE_NAME . '.uid', '='],
        'state' => [self::TABLE_NAME . '.state', '='],
        'enabled' => [self::TABLE_NAME . '.enabled', '='],
        'last_reported_at' => [self::TABLE_NAME . '.last_reported_at', '='],
        'created_at' => [self::TABLE_NAME . '.created_at', '='],
        'updated_at' => [self::TABLE_NAME . '.updated_at', '='],
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
                    $query->join(Plant::TABLE_NAME, self::TABLE_NAME . '.plant_id', '=', Plant::TABLE_NAME . '.id', $joinType);
                break;
            case WorkCenter::TABLE_NAME:
                if (!$this->joined($query, WorkCenter::TABLE_NAME))
                    $query->join(WorkCenter::TABLE_NAME, self::TABLE_NAME . '.target_id', '=', WorkCenter::TABLE_NAME . '.id', $joinType);
                break;
        }

        return $this;
    }
    public function getCount(): int
    {
        return $this->getQuery()->count();
    }
}
