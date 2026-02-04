<?php

namespace App\Http\Controllers\Web\Realtime;

use App\Extras\Traits\WorkCenterTrait;
use App\Http\Controllers\Controller;
use App\Models\DowntimeType;
use App\Models\Plant;
use Illuminate\Http\Request;

class DowntimeController extends Controller
{
    use WorkCenterTrait;
    //
    public function index(Request $request, $plantUid, $workCenterUid = null)
    {
        //TODO: Check user permission for selected plant
        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid);
        if (is_null($zoneData))
            abort(404);

        //TODO: Check user permission for selected plant

        /** @var \App\Models\Plant $plant */
        $plant = $zoneData['plant'];

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $zoneData['workCenter'];
        if(!$workCenter)
        {
            $zoneData = array_merge(
                $zoneData,
                [
                    'topBarTitle' => 'REAL TIME PRODUCTION MONITORING',
                ]
            );
            //no workcenter in plant
            return view('pages.web.realtime.no-work-center.downtime_no_workcenter',$zoneData);
        }
        if ($workCenter) {
            /** @var \App\Models\Production $currentProduction */
            $currentProduction = $workCenter->currentProduction;

            if ($currentProduction)
                $productionLines = $currentProduction->productionLines()->with(['productionOrder', 'part'])->orderBy('line_no', 'ASC')->get();
            else
                $productionLines = [];
        } else {
            $currentProduction = null;
            $productionLines = [];
        }

        $workCenters = $plant->onPlantDb()->workCenters;


        $machineDowntimes = $workCenter->downtimes()->with(['downtimeReasons'])->where('downtime_type_id', '=', DowntimeType::MACHINE_DOWNTIME)->get();
        $humanDowntimes = $workCenter->downtimes()->with(['downtimeReasons'])->where('downtime_type_id', '=', DowntimeType::HUMAN_DOWNTIME)->get();
        $workCenterDowntimes = $workCenter->workCenterDowntimes()->get();



        $viewData = array_merge(
            $zoneData,
            [
                'topBarTitle' => 'REAL TIME PRODUCTION MONITORING',
                'workCenters' => $workCenters,

                'production' => $currentProduction,
                'productionLines' => $productionLines,

                'machineDowntimes' => $machineDowntimes,
                'humanDowntimes' => $humanDowntimes,
                'workCenterDowntimes' => $workCenterDowntimes
            ]
        );
        return view('pages.web.realtime.downtime', $viewData);
    }
}
