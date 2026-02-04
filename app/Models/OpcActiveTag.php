<?php

namespace App\Models;

use App\Extras\Support\Opc\OpcTagConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned integer
 * 
 * @property int $plant_id Foreign Key (Plant): unsigned integer
 * @property int $opc_server_id Foreign Key (OpcServer): unsigned integer
 * 
 * @property string $tag string
 * @property string $data_type string
 * @property string $value string
 * @property string $prev_value string
 * @property string $set_value string
 * @property string $state tiny integer (0:Unknown, 1: OK, -1: Error, -2: Missing)
 * 
 * @property string $value_updated_at timestamp
 * @property string $created_at timestamp
 * @property string $updated_at timestamp
 */
class OpcActiveTag extends Model
{
    const TABLE_NAME = 'opc_active_tags';
    protected $table = self::TABLE_NAME;

    protected $guarded = [];

    const TAG_STATUS_OK = 1;
    const TAG_STATUS_UNKNOWN = 0;
    const TAG_STATUS_ERROR = -1;
    const TAG_STATUS_MISSING = -2;


    public function getConfig()
    {

        $config = new OpcTagConfig();
        $config->tag = $this->tag;
        $config->data_type = $this->data_type;
        $config->mode = is_null($this->set_value) ? OpcTagConfig::TAG_MODE_READ : OpcTagConfig::TAG_MODE_WRITE;
        $config->value = $this->set_value;
        
        return $config;
    }

    /**
     * @return int value needed to write to opc server (write mode)
     * @return null value not need to write to opc server (read mode)
     */
    public function getTargetValue(): ?int
    {
        /** @var \App\Models\Plant $plant */
        $plant = $this->plant;
        if (!$plant)
            return null;

        $opcTags = $plant->onPlantDb()->opcTags()->where('tag', '=', $this->tag)->get();
        /** @var \App\Models\OpcTag $opcTag */
        foreach ($opcTags as $opcTag) {
            $opcTag->updateValue($this->value, $this->value_updated_at);
        }
    }
    public function propagateTagEvent()
    {
        //pass data to receiver tag

        /** @var \App\Models\Plant $plant */
        $plant = $this->plant;
        if (!$plant)
            return;

        $opcTags = $plant->onPlantDb()->opcTags()->where('tag', '=', $this->tag)->get();

        /** @var \App\Models\OpcTag $opcTag */
        foreach ($opcTags as $opcTag) {
            $opcTag->updateValue($this->value, $this->value_updated_at);
        }
    }
    //Relationship

    public function opcServer()
    {
        return $this->belongsTo(OpcServer::class, 'opc_server_id', 'id');
    }
    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plant_id', 'id');
    }
}
