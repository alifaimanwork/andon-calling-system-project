<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calling extends Model
{
    use HasFactory;

    protected $table = 'callings';
    protected $connection = 'plant_base_default'; // Ensure the correct database connection

    // Mass assignable attributes
    protected $fillable = [
        'type', 'state', 'start_time', 'end_time', 'work_center_id',  'work_center_uid' , 'production_order_id', 'production_order' , 'shift_type_id' , 'shift_name' , 'part_id', 'part_number', 'part_name', 'line_no'
    ];

    // Relationships
    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plant_id', 'id');
    }

    public function workCenter()
    {
        return $this->belongsTo(WorkCenter::class, 'work_center_id');
    }

    public function productionLines()
    {
        return $this->hasMany(ProductionLine::class, 'production_order_id');
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class, 'shift_type_id');
    }

    public function part()
    {
        return $this->belongsTo(Part::class, 'part_id');
    }
}