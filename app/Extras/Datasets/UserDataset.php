<?php

declare(strict_types=1);

namespace App\Extras\Datasets;

use App\Extras\Datasets\Traits\DatatableTrait;
use App\Extras\Datasets\Traits\PlantDatabaseTrait;
use App\Extras\Datasets\Traits\QueryBuilderTrait;
use App\Models\Company;
use App\Models\Plant;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserDataset
{
    use QueryBuilderTrait;
    use DatatableTrait;

    const TABLE_NAME = User::TABLE_NAME;
    protected $table = self::TABLE_NAME;

    protected $datatableColumns = [
        'id' => [self::TABLE_NAME . '.id', '='],
        'full_name' => [self::TABLE_NAME . '.full_name', null],
        'staff_no' => [self::TABLE_NAME . '.staff_no', null],
        'designation' => [self::TABLE_NAME . '.designation', null],
        'email' => [self::TABLE_NAME . '.email', null],
        'company_name' => [Company::TABLE_NAME . '.name', null, [Company::TABLE_NAME, 'left']],
        'enabled' => [self::TABLE_NAME . '.enabled', null],
        'role' => [self::TABLE_NAME . '.role', null],
        'platform_access' => [null, null, ['plant_user', 'left']],
    ];

    function __construct()
    {
        $this->datatableColumns['platform_access'][0] = DB::raw('CONCAT("[",plant_user.web_permission,",",plant_user.terminal_permission,"]") AS platform_access');
    }

    private function applyFilters(Builder &$query): self
    {
        if (isset($this->filters['plant_id']) && isset($this->filters['origin_plant_id'])) {

            //TODO: simplify query
            $this->joinTable('plant_user');

            $query->where(function ($q) {
                $q->where(function ($q) {
                    $q->whereNull('plant_user.plant_id');
                    if (is_array($this->filters['origin_plant_id'])) {
                        $q->whereIn(self::TABLE_NAME . '.plant_id', $this->filters['origin_plant_id']);
                    } else {
                        $q->where(self::TABLE_NAME . '.plant_id', '=', $this->filters['origin_plant_id']);
                    }
                })->orWhere('plant_user.plant_id', '=', $this->filters['plant_id']);
            });
        } else {
            if (isset($this->filters['plant_id'])) {

                $this->joinTable('plant_user');
                $query->where(function ($q) {
                    $q->where('plant_user.plant_id', '=', $this->filters['plant_id'])
                        ->orWhereNull('plant_user.plant_id');
                });


                //pirotize by plant_id

                // $query->groupBy(self::TABLE_NAME . '.id');





                // $adminPlantIds = User::getCurrent()->getAdminPlants()->pluck('id');
                // $query->whereIn('plant_user.plant_id', $adminPlantIds);
            }
            if (isset($this->filters['origin_plant_id'])) {
                if (is_array($this->filters['origin_plant_id'])) {
                    $query->whereIn(self::TABLE_NAME . '.plant_id', $this->filters['origin_plant_id']);
                } else {
                    $query->where(self::TABLE_NAME . '.plant_id', '=', $this->filters['origin_plant_id']);
                }
            }
        }
        if (isset($this->filters['min_role'])) {
            $query->where(self::TABLE_NAME . '.role', '<=', $this->filters['min_role']);
        }
        //filter current user id
        if (isset($this->filters['except_current_user'])) {
            $query->whereNot(self::TABLE_NAME . '.id', $this->filters['except_current_user']);
        }

        if (isset($this->filters['role'])) {
            $query->where(self::TABLE_NAME . '.role', '=', $this->filters['role']);
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
        //apply select
        $this->applySelect($query)
            ->applyGroupBy($query)
            ->applyFilters($query);

        //apply join table
        foreach ($this->query_join_tables as $table => $join_table) {
            $this->applyJoinTable($query, $join_table->table, $join_table->join_type);
        }

        return $query;
    }
    private function applyJoinTable(Builder &$query, $table, $joinType = 'inner'): self
    {
        switch ($table) {
            case Company::TABLE_NAME:
                if (!$this->joined($query, Company::TABLE_NAME))
                    $query->join(Company::TABLE_NAME, $this->table . '.company_id', '=', Company::TABLE_NAME . '.id', $joinType);
                break;

            case 'plant_user':
                if (!$this->joined($query, 'plant_user')) {
                    if (isset($this->filters['plant_id'])) {

                        $query->leftJoin('plant_user', function ($q) {
                            $q->on($this->table . '.id', '=', 'plant_user.user_id');
                            $q->on(
                                'plant_user.plant_id',
                                '=',
                                DB::raw($this->filters['plant_id'])
                            );
                        });
                    } else
                        $query->join('plant_user', $this->table . '.id', '=', 'plant_user.user_id', $joinType);
                }
                break;
        }
        return $this;
    }
    public function getCount(): int
    {
        return $this->getQuery()->count();
    }
}
