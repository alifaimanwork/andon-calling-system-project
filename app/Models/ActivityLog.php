<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned long integer
 * 
 * @property int $user_id Foreign Key (User): unsigned integer
 * @property int $plant_id Foreign Key (Plant): unsigned integer
 * @property string $event_type string
 * @property string $event_title string
 * @property string $event_data text
 * 
 * @property string $created_at timestamp
 */
class ActivityLog extends Model
{
    const TABLE_NAME = 'activity_logs';
    protected $table = self::TABLE_NAME;

    protected $guarded = [];

    const UPDATED_AT = null;
}
