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
 * @property int $reject_group_id Foreign Key (rejectGroup): unsigned integer
 * 
 * @property string $name string
 * @property int $enabled tinyinteger (0: Disabled, 1: Enabled)
 * 
 * @property string $created_at timestamp
 * @property string $updated_at timestamp
 * 
 */

class RejectType extends Model  implements ModelDestroyable
{
    const TABLE_NAME = 'reject_types';
    protected $table = self::TABLE_NAME;

    public function isDestroyable(string &$reason = null): bool
    {
        //TODO, only return true when no other resource references to this
        return false;
    }
    //relationships

    //belongto plant_id
    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plants_id', 'id');
    }

    //belongto reject_group_id
    public function rejectGroup()
    {
        return $this->belongsTo(RejectGroup::class, 'reject_group_id', 'id');
    }

    //hasmany rejects
    public function rejects()
    {
        return $this->hasMany(Reject::class, 'reject_type_id', 'id');
    }

    //belongto part_reject_type
    public function partRejectTypes()
    {
        return $this->belongsToMany(Part::class, 'part_reject_type', 'reject_type_id', 'part_id');
    }
}
