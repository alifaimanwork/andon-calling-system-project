<?php

namespace App\Models;

use App\Extras\Support\ModelDestroyable;
use Illuminate\Database\Eloquent\Model;


/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned integer
 * 
 * @property int $plant_id Foreign Key (Plant): unsigned integer
 * 
 * @property string $uid string
 * @property string $name string
 * 
 * @property string $created_at timestamp
 * @property string $updated_at timestamp
 */
class Factory extends Model implements ModelDestroyable
{
    const TABLE_NAME = 'factories';
    protected $table = self::TABLE_NAME;

    public function isDestroyable(string &$reason = null): bool
    {
        if(!$this->workCenters()->first())
            return true;
        return false;
    }

    //relationship

    //TODO: factory - plant - company relationship
    public function workCenters()
    {
        return $this->hasMany(WorkCenter::class, 'factory_id', 'id');
    }
}
