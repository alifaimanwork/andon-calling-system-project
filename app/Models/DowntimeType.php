<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned integer
 * 
 * @property string $name string
 */
class DowntimeType extends Model
{
    const TABLE_NAME = 'downtime_types';
    protected $table = self::TABLE_NAME;

    //disable updated_at, created_at
    const CREATED_AT = null;
    const UPDATED_AT = null;

    //hardcoded id (refer seeder)
    const MACHINE_DOWNTIME = 1;
    const HUMAN_DOWNTIME = 2;



    //relationships

    //belongto downtimes
    public function downtimes()
    {
        return $this->hasMany(Downtime::class, 'downtime_type_id', 'id');
    }
}
