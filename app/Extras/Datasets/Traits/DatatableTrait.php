<?php

declare(strict_types=1);

namespace App\Extras\Datasets\Traits;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

trait DatatableTrait
{
    use QueryBuilderTrait;

    public function getDatatableColumns()
    {
        return $this->datatableColumns;
    }
    public function getSelectColumns($cols)
    {
        $refColumns = $this->getDatatableColumns();
        $selects = [];
        $added = [];

        if (is_null($cols)) {
            foreach ($refColumns as $col => $colDef) {
                if (gettype($colDef[0]) == 'string')
                    $selects[] = $colDef[0] . ' as ' . $col;
                else {
                    $selects[] = $colDef[0];
                }
            }
        } else {

            foreach ($cols as $col) {
                if (in_array($col, $added))
                    continue;

                $added[] = $col;
                if (isset($refColumns[$col])) {

                    if (gettype($refColumns[$col][0]) == 'string')
                        $selects[] = $refColumns[$col][0] . ' as ' . $col;
                    else {
                        $selects[] = $refColumns[$col][0];
                    }
                }
            }
        }
        return $selects;
    }
    public function autoJoin($cols)
    {
        $refColumns = $this->getDatatableColumns();
        $added = [];
        if (is_null($cols)) {
            foreach ($refColumns as $col => $colDef) {

                if (isset($colDef[2])) {
                    $table = $colDef[2][0];
                    $joinType = $colDef[2][1] ?? 'inner';
                    $this->joinTable($table, $joinType);
                }
            }
        } else {
            foreach ($cols as $col) {
                if (in_array($col, $added))
                    continue;

                $added[] = $col;
                if (isset($refColumns[$col], $refColumns[$col][2])) {
                    $table = $refColumns[$col][2][0];
                    $joinType = $refColumns[$col][2][1] ?? 'inner';
                    $this->joinTable($table, $joinType);
                }
            }
        }
    }
    public function getSearchMap($cols)
    {
        $refColumns = $this->getDatatableColumns();
        $searchMap = [];
        $added = [];

        if (is_null($cols)) {
            foreach ($refColumns as $col => $colDef) {
                $searchMap[$col] = $colDef;
            }
        } else {
            foreach ($cols as $col) {
                if (is_null($col))
                    continue;
                if (in_array($col, $added))
                    continue;

                $added[] = $col;
                if (isset($refColumns[$col]))
                    $searchMap[$col] = $refColumns[$col];
            }
        }
        return $searchMap;
    }

    private function applySearchParameters(Builder &$query, array $searches, array | null $columns): self
    {
        if (is_null($columns) || !is_array($searches))
            return $this;


        foreach ($searches as $search) {
            if (!isset($search['field'], $search['parameter']))
                continue;

            if (!isset($columns[$search['field']]))
                continue;

            $columnName = null;
            $searchMode = null;
            if (is_array($columns[$search['field']])) {
                $columnName = $columns[$search['field']][0];
                if (count($columns[$search['field']]) > 1)
                    $searchMode = $columns[$search['field']][1];
            } else
                $columnName = $columns[$search['field']];

            switch ($searchMode) {
                case '=':
                case '<':
                case '>':
                case '<=':
                case '>=':
                case '!=':
                    $query->where($columnName, $searchMode, $search['parameter']);
                    break;

                default:
                    $query->whereRaw('INSTR(' . $columnName . ',?)', [$search['parameter']]);
                    break;
            }
        }

        return $this;
    }

    public function datatable(Request $request, Builder $query = null, array $extra = []): array
    {


        $start = $request->start ?? null;
        $length = $request->length ?? null;

        //search
        $search = $request->search ?? null;
        $colDefs = $request->cols ?? null;


        //filters

        if (isset($request->filters) && is_array($request->filters)) {

            foreach ($request->filters as $key => $value) {
                $this->setFilters($key, $value, false);
            }
        }




        $columns = $this->getSearchMap($colDefs);

        if (is_null($this->query_select) || count($this->query_select) <= 0) {
            $selects = $this->getSelectColumns($colDefs);
            $this->select($selects);
        }
        //auto apply join require table
        $this->autoJoin($colDefs);

        if (is_null($query))
            $query = $this->getQuery();

        if (is_array($search))
            $this->applySearchParameters($query, $search, $columns);

        if (is_null($start) || is_null($length)) {
            //return full collection
            $data = $query->get()->toArray();
            $count = count($data);
            return [
                'recordsTotal' => $count,
                'recordsFiltered' => $count,
                'data' => $data
            ];
        }

        if (!is_numeric($start) || !is_numeric($length))
            return ['data' => []];


        //orders
        if (is_array($request->columns) && is_array($request->order)) {
            $cols = [];
            foreach ($request->columns as $column) {
                if (!is_array($column) || !isset($column['data']))
                    continue;

                $cols[] = $column['data'];
            }
            $ordered = [];
            foreach ($request->order as $order) {
                if (!is_array($order) || !isset($order['column']) || !is_numeric($order['column']))
                    continue;
                $ordering_col = intval($order['column']);
                if ($ordering_col >= count($cols) || $ordering_col < 0)
                    continue;

                $dir = (($order['dir'] ?? 'asc') == 'asc') ? 'asc' : 'desc';

                //check column in allowed cols
                if (!isset($columns[$cols[$ordering_col]]))
                    continue;

                //check already ordered
                if (in_array($cols[$ordering_col], $ordered))
                    continue;

                $query->orderBy($cols[$ordering_col], $dir);
                $ordered[] = $cols[$ordering_col];
            }
        }

        if ($length <= 0)
            $length = 1;

        $page = floor($start / $length) + 1;
        if ($page < 0)
            $page = 0;
        $paginated = $query->paginate($length, ['*'], 'page', $page);

        //add extra parameters to result
        if (is_array($extra)) {
            foreach ($extra as $key => $param) {
                if (!isset($result[$key]))
                    $result[$key] = $param;
            }
        }

        $autonumber = false;
        foreach ($colDefs as $colDef) {
            if (!is_null($colDef) && $colDef == '_no');
            $autonumber = true;
        }
        $data = $paginated->items();
        if ($autonumber) {
            //add numbering
            for ($n = 0; $n < count($data); $n++) {
                $data[$n]->_no = $n + 1 + $start;
            }
        }

        return [
            'recordsTotal' => $paginated->total(),
            'recordsFiltered' => $paginated->total(),
            'data' => $data
        ];
    }
}
