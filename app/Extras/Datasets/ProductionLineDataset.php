<?php

declare(strict_types=1);

namespace App\Extras\Datasets;

use App\Extras\Datasets\Traits\DatatableTrait;
use App\Extras\Datasets\Traits\PlantDatabaseTrait;
use App\Extras\Datasets\Traits\QueryBuilderTrait;
use App\Models\Part;
use App\Models\Plant;
use App\Models\Production;
use App\Models\ProductionLine;
use App\Models\ProductionOrder;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductionLineDataset
{
    use DatatableTrait;
    use PlantDatabaseTrait;

    const TABLE_NAME = ProductionLine::TABLE_NAME;
    protected $table = self::TABLE_NAME;

    protected $datatableColumns = [
        'id' => [self::TABLE_NAME . '.id', '='],

        'production_order_id' => [self::TABLE_NAME . '.production_order_id', null],

        'order_no' => [ProductionOrder::TABLE_NAME . '.order_no', null, [ProductionOrder::TABLE_NAME, 'left']],
        'work_center_id' => [ProductionOrder::TABLE_NAME . '.work_center_id', [ProductionOrder::TABLE_NAME, 'left']],

        'production_id' => [self::TABLE_NAME . '.production_id', null],
        'line_no' => [self::TABLE_NAME . '.line_no', null],

        'part_data' => [self::TABLE_NAME . '.part_data', null],

        'actual_output' => [self::TABLE_NAME . '.actual_output', null],
        'reject_count' => [self::TABLE_NAME . '.reject_count', null],
        'ok_count' => [self::TABLE_NAME . '.ok_count', null],

        'pending_count' => [self::TABLE_NAME . '.pending_count', null],
        'pending_ok' => [self::TABLE_NAME . '.pending_ok', null],
        'pending_ng' => [self::TABLE_NAME . '.pending_ng', null],
        'rework_status' => [self::TABLE_NAME . '.rework_status', null],

        'reject_summary' => [self::TABLE_NAME . '.reject_summary', null],

        'oee' => [self::TABLE_NAME . '.oee', null],
        'availability' => [self::TABLE_NAME . '.availability', null],
        'performance' => [self::TABLE_NAME . '.performance', null],
        'quality' => [self::TABLE_NAME . '.quality', null]
    ];

    private function applyFilters(Builder &$query): self
    {
        //define dataset filter here


        if (isset($this->filters['rework_status']))
            $query->where(self::TABLE_NAME . '.rework_status', '=', $this->filters['rework_status']);

        if (isset($this->filters['production_status'])) {
            $this->applyJoinTable($query, Production::TABLE_NAME);
            $query->where(Production::TABLE_NAME . '.status', '=', $this->filters['production_status']);
        }

        if (isset($this->filters['work_center_id'])) {
            $this->applyJoinTable($query, ProductionOrder::TABLE_NAME);
            $query->where(ProductionOrder::TABLE_NAME . '.work_center_id', '=', $this->filters['work_center_id']);
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
            case Production::TABLE_NAME:
                if (!$this->joined($query, Production::TABLE_NAME))
                    $query->join(Production::TABLE_NAME, $this->table . '.production_id', '=', Production::TABLE_NAME . '.id', $joinType);
                break;
            case ProductionOrder::TABLE_NAME:
                if (!$this->joined($query, ProductionOrder::TABLE_NAME))
                    $query->join(ProductionOrder::TABLE_NAME, $this->table . '.production_order_id', '=', ProductionOrder::TABLE_NAME . '.id', $joinType);
                break;
        }
        return $this;
    }
    public function getCount(): int
    {
        return $this->getQuery()->count();
    }
}
