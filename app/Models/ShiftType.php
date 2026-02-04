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

class ShiftType extends Model
{
    const TABLE_NAME = 'shift_types';
    protected $table = self::TABLE_NAME;

    const DAY_SHIFT = 1;
    const NIGHT_SHIFT = 2;

    const CREATED_AT = NULL;
    const UPDATED_AT = NULL;
    
    //relationships

    //hasmany productions
    public function productions()
    {
        return $this->hasMany(Production::class, 'shift_type_id', 'id');
    }

    //hasmany shifts
    public function shifts()
    {
        return $this->hasMany(Shift::class, 'shift_type_id', 'id');
    }
}
