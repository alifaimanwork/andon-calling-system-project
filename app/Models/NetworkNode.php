<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned integer
 * 
 * @property int $plant_id Foreign Key (Plant): unsigned integer
 * @property int $parent_node_id Foreign Key (NetworkNode): unsigned integer
 * 
 * @property string $name string
 * @property string $ip_address string
 * @property int $status tinyinteger (0: Disabled, 1: Enabled)
 * @property string $parameters text
 * 
 * @property string $created_at timestamp
 * @property string $updated_at timestamp
 */
class NetworkNode extends Model
{
    const TABLE_NAME = 'network_nodes';
    protected $table = self::TABLE_NAME; 

    //relationships

    //belongto plant_id
    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plant_id', 'id');
    }
    
//belongto parent_node_id
    public function parentNode()
    {
        return $this->belongsTo(NetworkNode::class, 'parent_node_id', 'id');
    }

    //hasmany child_nodes
    public function childNodes()
    {
        return $this->hasMany(NetworkNode::class, 'parent_node_id', 'id');
    }

}
