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
 * @property string $flag string
 * 
 * @property string $created_at timestamp
 * @property string $updated_at timestamp
 * 
 */
class Region extends Model
{
    const TABLE_NAME = 'regions';
    protected $table = self::TABLE_NAME; 

    //Utils
    public function getFlagUrl()
    {
        if (!is_null($this->flag) && strlen($this->flag) > 0)
            return asset('images/flags/' . $this->flag);

        return null;
    }
    //relationships

    public function plants()
    {
        return $this->hasMany(Plant::class, 'region_id', 'id');
    }
}
