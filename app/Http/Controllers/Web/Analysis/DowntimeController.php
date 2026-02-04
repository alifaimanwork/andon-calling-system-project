<?php

namespace App\Http\Controllers\Web\Analysis;

use App\Extras\Payloads\GenericRequestResult;
use App\Extras\Support\AnalysisDowntime;
use App\Extras\Traits\PlantTrait;
use App\Extras\Traits\WorkCenterTrait;
use App\Http\Controllers\Controller;
use App\Models\Plant;
use App\Models\WorkCenter;
use Illuminate\Http\Request;

class DowntimeController extends Controller
{
    //
    use WorkCenterTrait;
    use PlantTrait;
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

        $workCenters = $plant->onPlantDb()->workCenters;

        if (count($workCenters) == 0) {
            //no workcenter in plant
            $zoneData = array_merge(
                $zoneData,
                [
                    'topBarTitle' => 'OPERATIONAL ANALYSIS',
                ]
            );

            return view('pages.web.analysis.no-work-center.downtime_no_workcenter', $zoneData);
        }

        $viewData = array_merge(
            $zoneData,
            [
                'topBarTitle' => 'OPERATIONAL ANALYSIS',
                'workCenters' => $workCenters,
            ]
        );
        return view('pages.web.analysis.downtime', $viewData);
    }
    public function getData(Request $request, $plantUid)
    {
        $zoneData = $this->getPlant($plantUid);
        if (is_null($zoneData))
            abort(404);
        
        /** @var \App\Models\Plant $plant */
        $plant = $zoneData['plant'];

        $workCenterUid = $request->work_center_uid;

        if (!$workCenterUid)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid parameter.");

        $workCenter = $plant->onPlantDb()->workCenters()->where('enabled', 1)->where(WorkCenter::TABLE_NAME . '.uid', $workCenterUid)->first();

        if (!$workCenter)
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid parameter.");

        //TODO Validate Date
        $dateStart = $request->date_start;
        $dateEnd = $request->date_end;

        if ($request->format == 'print') {
            return view('pages.web.analysis.print.downtime', ['title' => 'Operational Analysis - Downtime', 'data' => AnalysisDowntime::create($plant, $workCenter->uid, $dateStart, $dateEnd)]);
        } else if ($request->format == 'download') {
            return  AnalysisDowntime::create($plant, $workCenter->uid, $dateStart, $dateEnd)->export();
        } else {
            return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK", AnalysisDowntime::create($plant, $workCenter->uid, $dateStart, $dateEnd));
        }
    }
}
