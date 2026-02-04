<?php

namespace App\Http\Controllers\Terminal;

use App\Extras\Payloads\PendingData;
use App\Extras\Traits\WorkCenterTrait;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkCenter;
use Illuminate\Http\Request;

class PendingController extends Controller
{
    use WorkCenterTrait;
    //
    public function index(Request $request, $plantUid, $workCenterUid)
    {
        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid);
        if (is_null($zoneData))
            abort(404);

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $zoneData['workCenter'];

        /** @var \App\Models\Production $production */
        $production = $workCenter->currentProduction;

        if (!$production || $workCenter->status != WorkCenter::STATUS_RUNNING) {
            //No production or not started, redirect to production planning
            return redirect()->route('terminal.production-planning.index', [$plantUid, $workCenterUid]);
        }

        $productionLines = null;
        if ($production)
            $productionLines = $production->productionLines()->orderBy('line_no')->get();

        $workCenterDowntimes = $workCenter->workCenterDowntimes()->get();
        if ($production)
            $activeDowntimeEvents = $production->getActiveDowntimeEvent();
        else
            $activeDowntimeEvents = [];

        $viewData = array_merge(
            $zoneData,
            [
                'production' => $production,
                'productionLines' => $productionLines,
                'user' => User::getCurrent(),
                'menuActive' => 'pending',
                'topBarTitle' => 'SMI IPOS TERMINAL',
                'workCenterDowntimes' => $workCenterDowntimes,
                'activeDowntimeEvents' => $activeDowntimeEvents,
                'downtimes' => $workCenter->downtimes
            ]
        );

        return view('pages.terminal.pending.index', $viewData);
    }

    public function setPending(Request $request, $plantUid, $workCenterUid)
    {
        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid, false);

        if (!$zoneData)
            abort(404);

        /** @var \App\Models\WorkCenter $workCenter  */
        $workCenter = $zoneData->workCenter;

        /** @var \App\Models\Plant $plant */
        $plant = $zoneData->plant;

        if (!isset($request->count, $request->production_line_id))
            abort(404); //quick check TODO: validate properly

        $pendingData = new PendingData($request->production_line_id, $request->count);

        return $workCenter->setPending($pendingData);
    }
}
