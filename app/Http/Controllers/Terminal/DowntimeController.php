<?php

namespace App\Http\Controllers\Terminal;

use App\Extras\Payloads\DowntimeData;
use App\Extras\Payloads\DowntimeReasonData;
use App\Extras\Payloads\GenericRequestResult;
use App\Extras\Traits\WorkCenterTrait;
use App\Http\Controllers\Controller;
use App\Models\DowntimeReason;
use App\Models\DowntimeType;
use App\Models\User;
use App\Models\WorkCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DowntimeController extends Controller
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
        $production = $production = $workCenter->currentProduction;

        if (!$production || $workCenter->status != WorkCenter::STATUS_RUNNING) {
            //No production or not started, redirect to production planning
            return redirect()->route('terminal.production-planning.index', [$plantUid, $workCenterUid]);
        }

        $productionLines = null;
        if ($production)
            $productionLines = $production->productionLines;

        $machineDowntimes = $workCenter->downtimes()->with(['downtimeReasons' => function ($q) {
            $q->where(DowntimeReason::TABLE_NAME . '.enabled', 1);
        }])->where('downtime_type_id', '=', DowntimeType::MACHINE_DOWNTIME)->get();
        $humanDowntimes = $workCenter->downtimes()->with(['downtimeReasons' => function ($q) {
            $q->where(DowntimeReason::TABLE_NAME . '.enabled', 1);
        }])->where('downtime_type_id', '=', DowntimeType::HUMAN_DOWNTIME)->get();

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
                'menuActive' => 'downtime',
                'topBarTitle' => 'SMI IPOS TERMINAL',
                'machineDowntimes' => $machineDowntimes,
                'humanDowntimes' => $humanDowntimes,
                'workCenterDowntimes' => $workCenterDowntimes,
                'activeDowntimeEvents' => $activeDowntimeEvents,
                'downtimes' => $workCenter->downtimes

            ]
        );

        return view('pages.terminal.downtime.index', $viewData);
    }

    public function setHumanDowntime(Request $request, $plantUid, $workCenterUid)
    {
        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid);
        if (is_null($zoneData))
            abort(404);

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $zoneData['workCenter'];

        if (!isset($request->set_state))
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid set state");

        //TODO: validate input
        $downtimeData = new DowntimeData($request->work_center_downtime_id, $request->downtime_id, $request->set_state);

        return $workCenter->setDowntime($downtimeData);
    }

    public function setDowntimeReason(Request $request, $plantUid, $workCenterUid)
    {
        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid);
        if (is_null($zoneData))
            abort(404);

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $zoneData['workCenter'];

        /** @var \App\Models\Production $production */
        $production = $workCenter->currentProduction;

        if (!$production) {
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_STATUS, "No production running");
        }

        $downtimeId = intval($request->downtime_id);
        $downtimeReasonId = intval($request->downtime_reason_id);


        //TODO: validation
        $downtimeReasonData = new DowntimeReasonData($production->id, $downtimeId, $downtimeReasonId, $request->user_input_reason);


        return $production->setDowntimeReason($downtimeReasonData);
    }
}
