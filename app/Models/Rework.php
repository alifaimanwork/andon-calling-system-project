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
 * @property int $user_id Foreign Key (user): unsigned integer
 * 
 * @property int $ok_count integer
 * @property int $ng_count integer
 * 
 * @property string $recorded_at timestamp
 */
class Rework extends Model
{
    const TABLE_NAME = 'reworks';
    protected $table = self::TABLE_NAME;

    const UPDATED_AT = null;
    const CREATED_AT = null;
    //relationships

    //belongto production_line_id
    public function productionLine()
    {
        return $this->belongsTo(ProductionLine::class, 'production_line_id', 'id');
    }

    //belongto user_id
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
