<?php

namespace App\Http\Controllers;

use App\Events\Opc\OpcTagValueChangedEvent;
use App\Models\OpcActiveTag;
use App\Models\OpcServer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OpcAdapterController extends Controller
{
    public function getActiveTags()
    {
        //Test route
        return [
            'data' => DB::table(OpcActiveTag::TABLE_NAME)
                ->select([
                    OpcActiveTag::TABLE_NAME . '.id as id',
                    OpcActiveTag::TABLE_NAME . '.opc_server_id as opc_server_id',
                    OpcServer::TABLE_NAME . '.hostname as hostname',
                    OpcServer::TABLE_NAME . '.port as port',
                    OpcActiveTag::TABLE_NAME . '.tag as tag',
                    OpcActiveTag::TABLE_NAME . '.value as value',
                    OpcActiveTag::TABLE_NAME . '.value_updated_at as value_updated_at'
                ])
                ->join(OpcServer::TABLE_NAME, OpcActiveTag::TABLE_NAME . '.opc_server_id', '=', OpcServer::TABLE_NAME . '.id')
                ->whereNotNull(OpcActiveTag::TABLE_NAME . '.plant_id')
                ->where('state', '>=', 0)
                ->get()
        ];
    }
    public function tagValueChanged(Request $request)
    {


        $result = new \stdClass();
        $result->result = 'ok';
        $result->data = $request->all(); //just return back payload for event clear


        //validate payload
        if (!(is_array($result->data)))
            return response('', 400);


        foreach ($result->data as $serverNode) {
            //validate
            if (!isset($serverNode['server_id'], $serverNode['data']) || !is_array($serverNode['data']))
                continue;
            $opcServer = OpcServer::with('opcActiveTags')->find($serverNode['server_id']);

            if (!$opcServer)
                continue;

            $activeTags = [];
            foreach ($opcServer->opcActiveTags as $tag) {
                $activeTags[$tag->tag] = $tag;
            }


            foreach ($serverNode['data'] as $tagNode) {
                //validate
                if (!isset($tagNode['tag'], $tagNode['data']) || !is_array($tagNode['data']))
                    continue;

                if (!isset($activeTags[$tagNode['tag']]))
                    continue;

                /** @var \App\Models\OpcActiveTag $activeTag */
                $activeTag = $activeTags[$tagNode['tag']];

                foreach ($tagNode['data'] as $event) {
                    //validate

                    if (!isset($event['value'], $event['updated_at']))
                        continue;

                    $dtUpdatedAt = \DateTime::createFromFormat('Y-m-d H:i:s', $event['updated_at']);
                    if (!$dtUpdatedAt)
                        continue;
                    $updatedAt = $dtUpdatedAt->format('Y-m-d H:i:s');
                    if (is_null($activeTag->value_updated_at) || $activeTag->value_updated_at < $updatedAt) {
                        $activeTag->prev_value = $activeTag->value;
                        $activeTag->value = $event['value'];
                        $activeTag->value_updated_at = $updatedAt;
                    }
                }

                $activeTag->save();
                try {
                    DB::table('opc_logs')->insert([
                        'server_id' => $activeTag->opc_server_id,
                        'tag' => $activeTag->tag,
                        'value' => $activeTag->value,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                } catch (Exception $ex) {
                }

                $activeTag->propagateTagEvent();
            }
        }

        return $result;
    }
    public function getConfigs()
    {
        //OPC Adapter get server configs
        $servers = OpcServer::get();

        $configs = [];

        /** @var \App\Models\OpcServer $server */
        foreach ($servers as  $server) {
            $configs[] = $server->getConfig();
        }
        return $configs;
    }

    public function updateStatus(Request $request)
    {
        $result = new \stdClass();
        $result->result = 'ok';



        if (!isset($request->data))
            return $result;

        $payload = json_decode($result->data);
        if (!$payload)
            return $result;

        $type = $payload->type ?? null;
        if (!$type) {
            Log::info('notype: ' . $result->data);
            return $result;
        }

        switch ($type) {
            case 'server':
                Log::info('server: ' . $result->data);
                break;
            case 'tag':
                Log::info('tag: ' . $result->data);
                break;
            default:
                Log::info('unknown: ' . $result->data);
                break;
        }

        //TODO: update tag/server state
        return $result;
    }
}
