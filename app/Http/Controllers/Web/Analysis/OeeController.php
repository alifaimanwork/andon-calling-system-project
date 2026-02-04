<?php

namespace App\Http\Controllers\Web\Analysis;

use App\Extras\Payloads\GenericRequestResult;
use App\Extras\Support\AnalysisOee;
use App\Extras\Traits\PlantTrait;
use App\Extras\Traits\WorkCenterTrait;
use App\Http\Controllers\Controller;
use App\Models\Plant;
use App\Models\WorkCenter;
use Illuminate\Http\Request;

class OeeController extends Controller
{
    use WorkCenterTrait;
    use PlantTrait;
    //
    public function index(Request $request, $plantUid, $workCenterUid = null)
    {
        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid);
        if (is_null($zoneData))
            abort(404);

        //TODO: Check user permission for selected plant

        /** @var \App\Models\Plant $plant */
        $plant = $zoneData['plant'];

        $workCenters = $plant->onPlantDb()->workCenters;

        if(count($workCenters) == 0)
        {
            //no workcenter in plant
            $zoneData = array_merge(
                $zoneData,
                [
                    'topBarTitle' => 'OPERATIONAL ANALYSIS',
                ]
            );
            return view('pages.web.analysis.no-work-center.oee_no_workcenter',$zoneData);
        }
        $viewData = array_merge(
            $zoneData,
            [
                'topBarTitle' => 'OPERATIONAL ANALYSIS',
                'workCenters' => $workCenters,
            ]
        );

        return view('pages.web.analysis.oee', $viewData);
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
            return view('pages.web.analysis.print.oee', ['title' => 'Operational Analysis - OEE', 'data' => AnalysisOee::create($plant, $workCenter->uid, $dateStart, $dateEnd)]);
        } else if ($request->format == 'download') {
            return  AnalysisOee::create($plant, $workCenter->uid, $dateStart, $dateEnd)->export();
        } else {
            return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK", AnalysisOee::create($plant, $workCenter->uid, $dateStart, $dateEnd));
        }

        
    }
}
