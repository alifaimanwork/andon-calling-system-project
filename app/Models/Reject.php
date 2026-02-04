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
 * @property int $reject_type_id Foreign Key (rejectType): unsigned integer
 * 
 * @property int $count unsigned integer
 * 
 * @property string $recorded_at timestamp
 * 
 */
class Reject extends Model
{
    const TABLE_NAME = 'rejects';
    protected $table = self::TABLE_NAME; 
    
    const UPDATED_AT = null;
    const CREATED_AT = null;


    protected $casts = ['recorded_at' => 'datetime'];
    //relationships

    //belongto production_line_id
    public function productionLine()
    {
        return $this->belongsTo(ProductionLine::class, 'production_line_id', 'id');
    }

    //belongto reject_type_id
    public function rejectType()
    {
        return $this->belongsTo(RejectType::class, 'reject_type_id', 'id');
    }

    //belongto user_id
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
