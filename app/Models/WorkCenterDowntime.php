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
 * @property int $work_center_id Foreign Key (Work Center): unsigned integer
 * @property int $opc_tag_id Foreign Key (OPC Tag): unsigned integer
 * 
 * @property int $state tiny int
 * @property string $value_updated_at timestamp
 * 
 * 
 */
class WorkCenterDowntime extends Model
{
    const TABLE_NAME = 'work_center_downtimes';
    protected $table = self::TABLE_NAME;

    const CREATED_AT = null;
    const UPDATED_AT = null;

    //relationship
    public function downtime()
    {
        return $this->belongsTo(Downtime::class, 'downtime_id', 'id');
    }

    public function workCenter()
    {
        return $this->belongsTo(WorkCenter::class, 'work_center_id', 'id');
    }

    public function opcTag()
    {
        return $this->belongsTo(OpcTag::class, 'opc_tag_id', 'id');
    }
}
