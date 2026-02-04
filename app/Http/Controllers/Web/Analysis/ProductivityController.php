<?php

namespace App\Http\Controllers\Web\Analysis;

use App\Extras\Payloads\GenericRequestResult;
use App\Extras\Support\AnalysisProductivity;
use App\Extras\Traits\PlantTrait;
use App\Extras\Traits\WorkCenterTrait;
use App\Http\Controllers\Controller;
use App\Models\Plant;
use App\Models\Production;
use App\Models\WorkCenter;
use Illuminate\Http\Request;

class ProductivityController extends Controller
{
    //
    use WorkCenterTrait;
    use PlantTrait;
    //
    public function index(Request $request, $plantUid, $workCenterUid = null)
    {
        $viewData = $this->getPlantWorkCenter($plantUid, $workCenterUid);
        if (is_null($viewData))
            abort(404);

        //TODO: Check user permission for selected plant

        /** @var \App\Models\Plant $plant */
        $plant = $viewData['plant'];

        $workCenters = $plant->onPlantDb()->workCenters;
        if (count($workCenters) == 0) {
            //no workcenter in plant
            $viewData = array_merge(
                $viewData,
                [
                    'topBarTitle' => 'OPERATIONAL ANALYSIS',
                ]
            );


            return view('pages.web.analysis.no-work-center.productivity_no_workcenter', $viewData);
        }
        $viewData = array_merge(
            $viewData,
            [
                'topBarTitle' => 'OPERATIONAL ANALYSIS',
                'workCenters' => $workCenters,
            ]
        );
        return view('pages.web.analysis.productivity', $viewData);
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
            return view('pages.web.analysis.print.productivity', ['title' => 'Operational Analysis - Productivity', 'data' => AnalysisProductivity::create($plant, $workCenter->uid, $dateStart, $dateEnd)]);
        } else if ($request->format == 'download') {
            return  AnalysisProductivity::create($plant, $workCenter->uid, $dateStart, $dateEnd)->export();
        } else {
            return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK", AnalysisProductivity::create($plant, $workCenter->uid, $dateStart, $dateEnd));
        }
    }
}
