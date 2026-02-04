<?php

declare(strict_types=1);

namespace App\Extras\Datasets\Traits;

use Illuminate\Database\Query\Builder;

trait QueryBuilderTrait
{
    protected $query_select = null;
    protected $query_custom_select = null;

    protected $query_group_by = null;
    protected $query_join_tables = [];
    protected $filters = [];

    public function setFilters(string $filter_name, $parameter, $overrideExisting = true): self
    {
        if ($overrideExisting)
            $this->filters[$filter_name] = $parameter;
        elseif (!isset($this->filters[$filter_name]))
            $this->filters[$filter_name] = $parameter;
        return $this;
    }

    function joined($query, $table)
    {
        $joins = $query->joins;
        if ($joins == null) {
            return false;
        }
        foreach ($joins as $join) {
            if ($join->table == $table) {
                return true;
            }
        }
        return false;
    }
    function joinTable(string $table, $join_type = 'inner', $overwrite = false): self
    {
        //add joining table definition to query_join_table
        $join_data = new \stdClass();
        $join_data->table = $table;
        $join_data->join_type = $join_type;
        if ($overwrite || !isset($this->query_join_tables[$table]))
            $this->query_join_tables[$table] = $join_data;
        return $this;
    }
    function customSelect(array | null $select): self
    {
        $this->query_custom_select = $select;
        return $this;
    }
    function select(array | null $select): self
    {
        $this->query_select = $select;
        return $this;
    }

    function groupBy(string | null $group_by): self
    {
        if (is_null($group_by)) {
            $this->query_group_by = null; //reset group by
            return $this;
        }
        if (is_null($this->query_group_by))
            $this->query_group_by = [];

        if (!in_array($group_by, $this->query_group_by))
            $this->query_group_by[] = $group_by;

        return $this;
    }

    private function applySelect(Builder &$query): self
    {
        $select = null;

        if (is_array($this->query_select)) {
            $select = [];
            $select = array_merge($select, $this->query_select);
        }

        if (is_array($this->query_custom_select)) {
            if (!is_array($select))
                $select = [];

            $select = array_merge($select, $this->query_custom_select);
        }


        if (is_array($select))
            $query->select($select);

        return $this;
    }
    private function applyGroupBy(Builder &$query): self
    {
        if (is_array($this->query_group_by)) {
            foreach ($this->query_group_by as $group_by) {
                $query->groupBy($group_by);
            }
        }
        return $this;
    }
}
