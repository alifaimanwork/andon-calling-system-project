<?php

namespace App\Models;

use App\Extras\Support\ModelDestroyable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned integer
 * 
 * @property int $plant_id Foreign Key (plant): unsigned integer
 * @property int $work_center_id Foreign Key (workCenter): unsigned integer
 * 
 * @property string $part_no string
 * @property int $line_no unsigned integer
 * @property string $name string
 * @property int $setup_time unsigned integer
 * @property int $cycle_time unsigned integer
 * @property int $packaging unsigned integer
 * @property float $reject_target float
 * @property string $side string
 * @property int $opc_part_id unsigned integer
 *
 * @property int $enabled tinyinteger (0: Disabled, 1: Enabled)
 * 
 * @property string $created_at timestamp
 * @property string $updated_at timestamp
 * 
 * */
class Part extends Model implements ModelDestroyable
{
    const TABLE_NAME = 'parts';
    protected $table = self::TABLE_NAME; 

    public function isDestroyable(string &$reason = null): bool
    {
        //TODO, only return true when no other resource references to this
        if(!$this->productionOrders()->first())
        {
            return true;
        }
        // dd($this->productionOrders()->get());
        return false;
    }

    //relationships

    public function workCenter()
    {
        return $this->belongsTo(WorkCenter::class, 'work_center_id', 'id');
    }

    //belongto plant_id
    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plants_id', 'id');
    }

    //belongto part_reject_type
    public function partRejectTypes()
    {
        return $this->belongsToMany(RejectType::class,'part_reject_type', 'part_id', 'reject_type_id');
    }

    //hasmany production_orders
    public function productionOrders()
    {
        return $this->hasMany(ProductionOrder::class, 'part_id', 'id');
    }
}
