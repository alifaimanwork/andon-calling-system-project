<?php

namespace App\Http\Controllers;

use App\Events\Terminal\DebugEchoEvent;
use App\Extras\Traits\PlantTrait;
use App\Extras\Traits\WorkCenterTrait;
use App\Http\Controllers\Controller;
use App\Models\MonitorClient;
use App\Models\MonitorClientLog;
use App\Models\Plant;
use App\Models\WorkCenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class NetClientController extends Controller
{
    use PlantTrait;
    //
    public function register(Request $request, $plantUid, $clientUid)
    {
        $zoneData = $this->getPlant($plantUid);
        if (is_null($zoneData))
            abort(404);

        /** @var \App\Models\Plant $plant */
        $plant = $zoneData['plant'];

        if ($clientUid && $clientUid == '-') {
            //unregister
            if (isset($_COOKIE['monitor_uid'])) {
                unset($_COOKIE['monitor_uid']);
                setcookie('monitor_uid', null, time() - 3600, '/');
            }
        }

        /** @var \App\Models\MonitorClient $monitorClient */
        $monitorClient = $plant->onPlantDb()->monitorClients()->where('enabled', 1)->where('uid', $clientUid)->firstOrFail();
        $workCenter = $plant->workCenters()->where('id', $monitorClient->target_id)->firstOrFail();

        setcookie('monitor_uid', $monitorClient->uid, time() + 86400 * 30, '/');


        //redirect
        if ($monitorClient->client_type == MonitorClient::CLIENT_TYPE_DASHBOARD) {
            return redirect(route('dashboard.index', [$plant->uid, $workCenter->uid]));
        } elseif ($monitorClient->client_type == MonitorClient::CLIENT_TYPE_TERMINAL) {
            return redirect(route('terminal.index', [$plant->uid, $workCenter->uid]));
        } else {
            abort(404);
        }
    }
    public function report(Request $request, $plantUid, $clientUid)
    {
        $zoneData = $this->getPlant($plantUid);
        if (is_null($zoneData))
            abort(404);


        $validator = Validator::make($request->all(), ['state' => 'integer|required']);
        if ($validator->fails())
            abort(404);


        /** @var \App\Models\Plant $plant */
        $plant = $zoneData['plant'];

        /** @var \App\Models\MonitorClient $monitorClient */
        $monitorClient = $plant->onPlantDb()->monitorClients()->where('uid', $clientUid)->where('enabled', 1)->where(
            function (Builder $q) {
                $q->where('client_type', MonitorClient::CLIENT_TYPE_DASHBOARD)->orWhere('client_type', MonitorClient::CLIENT_TYPE_TERMINAL);
            }
        )->firstOrFail();

        $lastState = $monitorClient->state;
        $lastReportedAt = \DateTime::createFromFormat('Y-m-d H:i:s', $monitorClient->last_reported_at);


        $serverData = [
            'SERVER_ADDR' => $_SERVER['SERVER_ADDR'] ?? null,
            'SERVER_PORT' => $_SERVER['SERVER_PORT'] ?? null,
            'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? null,
            'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'] ?? null,
        ];

        $monitorClient->state = $request->state;
        $monitorClient->last_reported_at = date('Y-m-d H:i:s');
        $lastClientInfo = $monitorClient->client_info;
        $monitorClient->client_info = json_encode($serverData);
        $monitorClient->save();


        setcookie('monitor_uid', $monitorClient->uid, time() + 86400 * 30, '/');


        if ($lastState != $monitorClient->state || !$lastReportedAt || (time() - $lastReportedAt->getTimestamp() > 60) || $lastClientInfo != $monitorClient->client_info) {
            //update log
            $monitorClientLog = new MonitorClientLog();
            $monitorClientLog->monitor_client_id = $monitorClient->id;
            $monitorClientLog->state = $monitorClient->state;
            $monitorClientLog->recorded_at = $monitorClient->last_reported_at;
            $monitorClientLog->client_info = $monitorClient->client_info;
            $monitorClientLog->setConnection($plant->getPlantConnection())->save();
        }

        return ['result' => 'ok'];
    }
}
