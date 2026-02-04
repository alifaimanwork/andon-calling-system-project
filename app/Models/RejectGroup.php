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

class RejectGroup extends Model
{
    const TABLE_NAME = 'reject_groups';
    protected $table = self::TABLE_NAME; 

    const CREATED_AT = null;
    const UPDATED_AT = null;
 
    const REJECT_SETTING = 1;
    const REJECT_MATERIAL = 2;
    const REJECT_PROCESS = 3;

    //relationships

    //hasmany reject_types
    public function rejectTypes()
    {
        return $this->hasMany(RejectType::class, 'reject_group_id', 'id');
    }
}
