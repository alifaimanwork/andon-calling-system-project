<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned integer
 * 
 * @property string $work_center_id Foreign Key(WorkCenter): unsigned integer
 * @property string $opc_tag_id  Foreign Key(OpcTag): unsigned integer
 * @property string $production_line_id  Foreign Key(ProductionLine): unsigned integer
 * 
 * @property string $line_no  Foreign Key(OpcTag): unsigned integer
 * @property int $count integer
 * @property int $tag_value
 * @property int $work_center_status
 * 
 * @property string $recorded_at timestamp
 */
class CounterLog extends Model
{
    // use HasFactory;
    const TABLE_NAME = 'counter_logs';
    protected $table = self::TABLE_NAME;

    const UPDATED_AT = null;
    const CREATED_AT = null;
    //relationships

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
