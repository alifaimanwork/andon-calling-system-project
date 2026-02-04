<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned integer
 * 
 * @property int $work_center_id Foreign Key (WorkCenter): unsigned integer
 * @property int $downtime_id Foreign Key (Downtime): unsigned integer
 * @property int $opc_tag_id Foreign Key (OpcTag): unsigned integer
 * 
 * @property int $state tiny integer (0: Inactive, 1: Active)
 * 
 * @property string $recorded_at timestamp
 */
class DowntimeStateLog extends Model
{
    const TABLE_NAME = 'downtime_state_logs';
    protected $table = self::TABLE_NAME; 

    //disable updated_at, created_at
    const CREATED_AT = null;
    const UPDATED_AT = null;

    //relationships

    //belongto downtime_id
    public function downtime()
    {
        return $this->belongsTo(Downtime::class, 'downtime_id', 'id');
    }

    //belongto work_center_id
    public function workCenter()
    {
        return $this->belongsTo(WorkCenter::class, 'work_center_id', 'id');
    }

    //belongto opc_tag_id
    public function opcTag()
    {
        return $this->belongsTo(OpcTag::class, 'opc_tag_id', 'id');
    }
}
