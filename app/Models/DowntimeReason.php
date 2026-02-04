<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned integer
 * 
 * @property int $downtime_id Foreign Key (Downtime): unsigned integer
 * 
 * @property string $reason string
 * @property int $enable_user_input tiny integer (0: Disabled, 1: Enabled)
 * @property int $enabled tinyinteger (0: Disabled, 1: Enabled)
 * 
 * @property string $created_at timestamp
 * @property string $updated_at timestamp
 */
class DowntimeReason extends Model
{
    const TABLE_NAME = 'downtime_reasons';
    protected $table = self::TABLE_NAME; 

    //relationships

    //belongto plant_id
    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plant_id', 'id');
    }

    //belongsTo
    public function downtime()
    {
        return $this->belongsTo(Downtime::class, 'downtime_id', 'id');
    }
}
