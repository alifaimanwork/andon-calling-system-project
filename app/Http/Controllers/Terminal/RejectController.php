<?php

namespace App\Http\Controllers\Terminal;

use App\Extras\Payloads\RejectData;
use App\Extras\Payloads\GenericRequestResult;
use App\Extras\Traits\WorkCenterTrait;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkCenter;
use Illuminate\Http\Request;

class RejectController extends Controller
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


        /** @var \App\Models\Production $currentProduction */
        $currentProduction = $workCenter->currentProduction;

        $workCenterDowntimes = $workCenter->workCenterDowntimes()->get();
        if ($currentProduction)
            $activeDowntimeEvents = $currentProduction->getActiveDowntimeEvent();
        else
            $activeDowntimeEvents = [];

        $viewData = array_merge(
            $zoneData,
            [
                'production' => $production,
                'productionLines' => $productionLines,
                'user' => User::getCurrent(),
                'menuActive' => 'reject',
                'topBarTitle' => 'SMI IPOS TERMINAL',
                'workCenterDowntimes' => $workCenterDowntimes,
                'activeDowntimeEvents' => $activeDowntimeEvents,
                'downtimes' => $workCenter->downtimes
            ]
        );

        return view('pages.terminal.reject.index', $viewData);
    }

    public function setReject(Request $request, $plantUid, $workCenterUid)
    {
        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid, false);

        if (!$zoneData)
            abort(404);

        /** @var \App\Models\WorkCenter $workCenter  */
        $workCenter = $zoneData->workCenter;

        /** @var \App\Models\Plant $plant */
        $plant = $zoneData->plant;

        //TODO: Validate
        /*
        {
            "production_line_id":1,
            "reject_type_id":2,
            "count":3
        }
        */
        $rejectData = [];

        if (!isset($request->data, $request->production_line_id) || !is_array($request->data))
            abort(404); //quick check TODO: validate properly

        foreach ($request->data as $rejectDataInput) {
            if (!isset($rejectDataInput['reject_type_id'], $rejectDataInput['count']))
                continue;
            //TODO: reject data validation
            if ($rejectDataInput['count'] <= 0)
                continue;

            $rejectData[] = new RejectData($request->production_line_id, $rejectDataInput['reject_type_id'], $rejectDataInput['count']);
        }

        return $workCenter->setReject($rejectData);
    }
}
