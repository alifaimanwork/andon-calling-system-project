<?php

namespace App\Http\Controllers\Dashboard;

use App\Events\Terminal\DebugEchoEvent;
use App\Extras\Traits\WorkCenterTrait;
use App\Http\Controllers\Controller;
use App\Models\Plant;
use App\Models\WorkCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class DebugController extends Controller
{
    use WorkCenterTrait;
    //
    public function index(Request $request, $plantUid, $workCenterUid)
    {
        //$serverIp = $request->server('HOST');

        $serverData = [
            'SERVER_ADDR' => $_SERVER['SERVER_ADDR'] ?? null,
            'SERVER_PORT' => $_SERVER['SERVER_PORT'] ?? null,
            'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? null,
            'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'] ?? null,
        ];


        /** @var \App\Models\Plant $plant */
        $plant = Plant::where('uid', $plantUid)->firstOrFail();

        if (!$plant)
            abort(404);

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $plant->onPlantDb()->workCenters()->where(WorkCenter::TABLE_NAME . '.uid', $workCenterUid)->firstOrFail();
        if (!$workCenter)
            abort(404);


        /** @var \App\Models\Production $currentProduction */
        $currentProduction = $workCenter->currentProduction;

        if ($currentProduction)
            $productionLines = $currentProduction->productionLines()->with(['productionOrder', 'part'])->get();
        else
            $productionLines = [];

        $viewData = [
            'plant' => $plant,
            'workCenter' => $workCenter,
            'production' => $currentProduction,
            'productionLines' => $productionLines,
            'serverData' => $serverData,
        ];
        return view('pages.debug.network', $viewData);
    }
    public function echoTest(Request $request, $plantUid, $workCenterUid)
    {
        /** @var \App\Models\Plant $plant */
        $plant = Plant::where('uid', $plantUid)->firstOrFail();

        if (!$plant)
            return response(['result' => 'error', 'message' => 'invalid plant'], 404);

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $plant->onPlantDb()->workCenters()->where(WorkCenter::TABLE_NAME . '.uid', $workCenterUid)->firstOrFail();
        if (!$workCenter)
            return response(['result' => 'error', 'message' => 'invalid work center'], 404);

        if (!isset($request->debug_channel, $request->data))
            abort(400);


        $payload = $request->data;

        $testSize = $request->test_size ?? 1024;
        if ($testSize > 10240)
            $testSize = 10240;
        $payload['dummy'] = $this->generateRandom($testSize);

        event(new DebugEchoEvent($plant, $workCenter, $request->debug_channel, $payload));
        return $payload;
    }
    private function generateRandom($length)
    {
        $data = "";
        $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        for ($n = 0; $n < $length; $n++) {
            $data .= $charset[rand(0, strlen($charset) - 1)];
        }
        return $data;
    }
}
