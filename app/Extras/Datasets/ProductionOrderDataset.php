<?php

declare(strict_types=1);

namespace App\Extras\Datasets;

use App\Extras\Datasets\Traits\DatatableTrait;
use App\Extras\Datasets\Traits\PlantDatabaseTrait;
use App\Extras\Datasets\Traits\QueryBuilderTrait;
use App\Models\Part;
use App\Models\Plant;
use App\Models\ProductionOrder;
use App\Models\ShiftType;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductionOrderDataset
{
    use DatatableTrait;
    use PlantDatabaseTrait;

    const TABLE_NAME = ProductionOrder::TABLE_NAME;
    protected $table = self::TABLE_NAME;

    protected $datatableColumns = [
        'id' => [self::TABLE_NAME . '.id', '='],
        'plant_id' => [self::TABLE_NAME . '.plant_id', null],
        'part_id' => [self::TABLE_NAME . '.part_id', null],
        'work_center_id' => [self::TABLE_NAME . '.work_center_id', null],

        'pps_seq' => [self::TABLE_NAME . '.pps_seq', null],
        'pps_plant' => [self::TABLE_NAME . '.pps_plant', null],
        'pps_factory' => [self::TABLE_NAME . '.pps_factory', null],
        'pps_line' => [self::TABLE_NAME . '.pps_line', null],
        'pps_part_no' => [self::TABLE_NAME . '.pps_part_no', null],
        'pps_part_name' => [self::TABLE_NAME . '.pps_part_name', null],

        'pps_shift' => [self::TABLE_NAME . '.pps_shift', null],
        'order_no' => [self::TABLE_NAME . '.order_no', null],

        'plan_start' => [self::TABLE_NAME . '.plan_start', null],
        'plan_finish' => [self::TABLE_NAME . '.plan_finish', null],

        'plan_quantity' => [self::TABLE_NAME . '.plan_quantity', null],
        'unit_of_measurement' => [self::TABLE_NAME . '.unit_of_measurement', null],

        'pps_data' => [self::TABLE_NAME . '.pps_data', null],
        'pps_filename' => [self::TABLE_NAME . '.pps_filename', null],

        'actual_output' => [self::TABLE_NAME . '.actual_output', null],
        'status' => [self::TABLE_NAME . '.status', '='],

        'line_no' => [Part::TABLE_NAME . '.line_no', null, [Part::TABLE_NAME, 'left']]
    ];

    private function applyFilters(Builder &$query): self
    {
        //define dataset filter here


        if (isset($this->filters['status']))
            $query->where(self::TABLE_NAME . '.status', '=', $this->filters['status']);

        if (isset($this->filters['work_center_id']))
            $query->where(self::TABLE_NAME . '.work_center_id', '=', $this->filters['work_center_id']);

        if (isset($this->filters['shift_type_id'])) {
            $shiftType = ShiftType::where('id', $this->filters['shift_type_id'])->first();
            if ($shiftType)
                $query->where(self::TABLE_NAME . '.pps_shift', '=', $shiftType->label);
        }
        if (isset($this->filters['production_date'])) {
            $query->whereDate(self::TABLE_NAME . '.plan_start', '<=', $this->filters['production_date'])
                ->WhereDate(self::TABLE_NAME . '.plan_finish', '>=', $this->filters['production_date']);
        }

        if (isset($this->filters['today'])) {
            //Tempfix
            /** @var \App\Models\Plant $plant */
            $plant = Plant::where('uid', '=', 'iav-rayong')->first();

            //$localtime = $plant->getLocalDateTime();
            $now = new \DateTime();
            $todayStartTime = $now->format('Y-m-d') . ' 00:00:00';
            $now->add(new \DateInterval('P1D'));
            $todayEndTime = $now->format('Y-m-d') . ' 00:00:00';

            $query->where(self::TABLE_NAME . '.plan_start', '>=', $todayStartTime)
                ->where(self::TABLE_NAME . '.plan_start', '<', $todayEndTime);
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
            case Part::TABLE_NAME:
                if (!$this->joined($query, Part::TABLE_NAME))
                    $query->join(Part::TABLE_NAME, $this->table . '.part_id', '=', Part::TABLE_NAME . '.id', $joinType);
                break;
        }
        return $this;
    }
    public function getCount(): int
    {
        return $this->getQuery()->count();
    }
}
