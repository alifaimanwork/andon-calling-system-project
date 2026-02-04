<?php

namespace App\Http\Controllers\Terminal;

use App\Extras\Support\DieChangeInfo;
use App\Extras\Traits\TerminalRoutingTrait;
use App\Extras\Traits\WorkCenterTrait;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkCenter;
use Illuminate\Http\Request;

class TerminalController extends Controller
{
    use WorkCenterTrait;
    use TerminalRoutingTrait;
    //
    public function index(Request $request, $plantUid, $workCenterUid)
    {

        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid);
        if (is_null($zoneData))
            abort(404);

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $zoneData['workCenter'];

        //automatic handle redirection according to current workcenter status
        if(!User::getCurrent()->isTerminalOperator($plantUid, $workCenterUid)){
            return redirect()->route('terminal.rework.index', [$plantUid, $workCenterUid]);
        }

        if ($shouldRoute = $this->checkTerminalRoute($workCenter)) {
            return $shouldRoute;
        }

        abort(404);
    }
    public function getTerminalData(Request $request, $plantUid, $workCenterUid)
    {
        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid);
        if (is_null($zoneData))
            abort(404);

        /** @var \App\Models\Plant $plant */
        $plant = $zoneData['plant'];

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $zoneData['workCenter'];

        $responseData = [
            'plantId' => $plant->id,
            'plantUid' => $plant->uid,
            'workCenterUid' => $workCenter->uid,
        ];

        /** @var \App\Models\Production $currentProduction */
        $currentProduction = $workCenter->currentProduction()->first();
        if ($currentProduction) {
            $responseData['production'] = $currentProduction->toArray();
            $responseData['productionLines'] = $currentProduction->productionLines()->with('productionOrder')->get()->toArray();
        } else {
            $responseData['production'] = null;
            $responseData['productionLines'] = [];
        }
        $responseData['workCenter'] = $workCenter->toArray();

        //Reply data similar to WorkCenterDataUpdatedEvent broadcast
        return $responseData;
    }
}
