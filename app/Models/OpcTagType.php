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
 * 
 * */
class OpcTagType extends Model
{
    const TABLE_NAME = 'opc_tag_types';
    protected $table = self::TABLE_NAME; 

    const CREATED_AT = null;
    const UPDATED_AT = null;

    const TAG_DIE_CHANGE = 1;
    const TAG_BREAK = 2;
    const TAG_PART_NUMBER = 3;
    const TAG_COUNTER = 4;
    const TAG_DOWNTIME = 5;
    const TAG_HUMAN_DOWNTIME = 6;
    const TAG_ON_PRODUCTION = 7;

    //relationships

    //hasmany opc_tags
    public function opcTags()
    {
        return $this->hasMany(OpcTag::class, 'opc_tag_type_id', 'id');
    }
    
}
