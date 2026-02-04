<?php

namespace App\Models;

use App\Extras\Payloads\GenericRequestResult;
use App\Extras\Support\ModelDestroyable;
use App\Extras\Utils\ModelUtils;
use App\Jobs\ForceOpcResync;
use App\Jobs\SignalOpcSettingUpdated;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned integer
 * 
 * @property string $name string 
 * @property string $hostname string
 * @property int $port unsigned integer
 * @property string $configuration_data text
 * @property string $tags text
 * 
 * @property string $tags_updated_at timestamp
 * @property string $created_at timestamp
 * @property string $updated_at timestamp
 * 
 */

class OpcServer extends Model implements ModelDestroyable
{
    const TABLE_NAME = 'opc_servers';
    protected $table = self::TABLE_NAME;

    protected $guarded = [];

    public static function getOpcAdapterBaseUrl()
    {
        return env('OPC_ADAPTER_REQUEST_URL', 'http://127.0.0.1:8000');
    }

    //Utils
    public function syncToAllPlants()
    {

        //TODO: only sync to related plant
        //Sync to all ATM
        $plants = Plant::get();
        foreach ($plants as $plant) {
            $plant->loadAppDatabase();
            $dst = OpcServer::on($plant->getPlantConnection())->find($this->id);
            if (!$dst) {
                $dst = new OpcServer();
                $dst->connection = $plant->getPlantConnection();
            }
            ModelUtils::copyFields($this, $dst);
            $dst->save();
        }
    }
    public function deleteFromAllPlants()
    {
        $plants = Plant::get();
        foreach ($plants as $plant) {
            $plant->loadAppDatabase();
            OpcServer::on($plant->getPlantConnection())->where('id', $this->id)->delete();
        }
    }

    public function getConfig()
    {
        // TODO: DTO
        $config = new \stdClass();
        $config->id = $this->id;
        $config->hostname = $this->hostname;
        $config->port = $this->port;
        $config->tags = [];


        /** @var \App\Models\OpcActiveTag $activeTag */
        foreach ($this->opcActiveTags as $activeTag) {
            $config->tags[] = $activeTag->getConfig();
        }

        //TODO: Additional opc config e.g. security,crediential

        return $config;
    }

    public function isDestroyable(string &$reason = null): bool
    {
        //TODO, only return true when no other resource references to this
        if (!$this->opcTags()->first())
            return true;

        if (!$this->opcActiveTags()->first())
            return true;
        return false;
    }
    public function resyncOpcServer(): GenericRequestResult
    {
        dispatch(new ForceOpcResync($this));

        return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK");
    }

    public function syncTags(): GenericRequestResult
    {
        try {
            $response = Http::post(
                self::getOpcAdapterBaseUrl(),
                [
                    'function' => 'fetch_all_tags',
                    'data' => [
                        'host' =>  $this->hostname,
                        'port' =>  $this->port
                    ]
                ]
            );
        } catch (Exception $ex) {
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_STATUS, "Fail to connect to OPC Adapter: \r\n" . $ex->getMessage());
        }

        $data = json_decode($response->body());
        if (!$data)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_STATUS, 'Invalid Response');

        // dd($data);
        if (!$data->result || $data->result != 'ok' || !$data->data)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_STATUS, "Fail to fetch tag from opc server: \r\n" . $data->data ?? '');



        //sync
        $excludedName = ['Server', 'SYSTEM'];

        $tags = [];

        foreach ($data->data->childs as $rootElement) {
            $browseName = $rootElement->browse_name ?? null;
            if (!$browseName || in_array($browseName, $excludedName) || !isset($rootElement->childs) || !is_array($rootElement->childs))
                continue;

            $tagGroups = [];

            //$this->warn($browseName);
            foreach ($rootElement->childs as $key => $child) {
                $browseName = $child->browse_name ?? null;
                $tag =  $child->tag ?? null;

                if (!$browseName || !$tag || !is_string($tag) || !is_string($browseName) || $browseName[0] == '$' || $child->class != 'Variable')
                    continue;

                if ($tag != trim($tag))
                    continue;

                //$this->info('[' . $child->browse_name . "] " . $tag);
                $tags[] = $child;
            }
        }


        $opcTags = $this->opcActiveTags()->get();
        $oldTags = [];


        /** @var \App\Models\OpcActiveTag $opcTag */
        foreach ($opcTags as $opcTag) {
            $oldTags[$opcTag->tag] = $opcTag;
        }

        $newTags = [];

        $availableTags = [];

        $n = 0;

        $updatedCount = 0;
        $newCount = 0;
        $missingCount = 0;

        foreach ($tags as $tag) {
            $availableTags[] = $tag->tag;
            if (isset($oldTags[$tag->tag])) {

                /** @var \App\Models\OpcActiveTag $opcTag */
                $opcTag = $oldTags[$tag->tag];
                $opcTag->data_type = $tag->data_type;

                if ($opcTag->isDirty())
                    $updatedCount++;

                $opcTag->state = OpcActiveTag::TAG_STATUS_OK;
                $opcTag->save();

                continue;
            }

            $newTags[] = $tag->tag;

            $newCount++;



            (new OpcActiveTag([
                'opc_server_id' => $this->id,
                'plant_id' => null,
                'tag' => $tag->tag,
                'data_type' => $tag->data_type,
                'state' => OpcActiveTag::TAG_STATUS_OK
            ]))->save();
        }



        /** @var \App\Models\OpcActiveTag $opcTag */
        foreach ($oldTags as $tag => $opcTag) {
            if (!in_array($tag, $availableTags)) {
                $missingCount++;

                /** @var \App\Models\OpcActiveTag $opcTag */
                $opcTag = $oldTags[$opcTag->tag];

                $opcTag->state = OpcActiveTag::TAG_STATUS_MISSING;
                $opcTag->save();
            }
        }

        dispatch(new SignalOpcSettingUpdated());

        return new GenericRequestResult(GenericRequestResult::RESULT_OK, "ok", [
            "total" => count($tags),
            "new" => $newCount,
            "updated" => $updatedCount,
            "missing" => $missingCount
        ]);
    }

    //relationships

    //hasmany opc_tags
    public function opcTags()
    {
        return $this->hasMany(OpcTag::class, 'opc_server_id', 'id');
    }

    public function opcActiveTags()
    {
        return $this->hasMany(OpcActiveTag::class, 'opc_server_id', 'id');
    }
}
