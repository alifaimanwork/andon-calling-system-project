<?php

namespace App\Models;

use App\Extras\Support\ModelDestroyable;
use Illuminate\Database\Eloquent\Model;


/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned integer
 * 
 * @property int $monitor_client_id 
 * @property int $target_id Foreign Key (WorkCenterId): unsigned integer
 * @property int $state websocket 
 * @property string $recorded_at timestamp
 */
class MonitorClientLog extends Model
{
    const TABLE_NAME = 'monitor_client_logs';
    protected $table = self::TABLE_NAME;

    const UPDATED_AT = null;
    const CREATED_AT = null;

    //utils

    //relationship

    public function monitorClient()
    {
        return $this->belongsTo(MonitorClient::class, 'monitor_client_id', 'id');
    }
}
