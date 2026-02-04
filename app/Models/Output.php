<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned integer
 * 
 * @property int $production_line_id Foreign Key (productionLine): unsigned integer
 * 
 * @property int $count unsigned integer
 * 
 * @property string $recorded_at timestamp
 * 
 * */

class Output extends Model
{
    const TABLE_NAME = 'outputs';
    protected $table = self::TABLE_NAME; 

    //relationships

    //belongto production_line_id
    public function productionLine()
    {
        return $this->belongsTo(ProductionLine::class, 'production_line_id', 'id');
    }
}
