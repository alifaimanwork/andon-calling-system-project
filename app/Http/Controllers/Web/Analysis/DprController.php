<?php

namespace App\Http\Controllers\Web\Analysis;

use App\Models\Plant;
use App\Models\ShiftType;
use App\Models\Production;
use App\Models\WorkCenter;
use Illuminate\Http\Request;
use App\Extras\Support\AnalysisDpr;
use App\Extras\Utils\ExcelTemplate;
use App\Http\Controllers\Controller;
use App\Extras\Traits\WorkCenterTrait;
use App\Extras\Payloads\GenericRequestResult;

class DprController extends Controller
{
    use WorkCenterTrait;
    //
    public function index(Request $request, $plantUid, $workCenterUid = null, $lineNo = 1)
    {
        $zoneData = $this->getPlantWorkCenterLine($plantUid, $workCenterUid, $lineNo);
        if (is_null($zoneData))
            abort(404);
        /** @var \App\Models\Plant $plant */
        // $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant = $zoneData['plant'];
        //TODO: Check user permission for selected plant

        $workCenters = $plant->onPlantDb()->workCenters;
        $shiftTypes = ShiftType::get();

        $viewData = array_merge(
            $zoneData,
            [
                'topBarTitle' => 'OPERATIONAL ANALYSIS',
                'plant' => $plant,
                'workCenters' => $workCenters,
                'shiftTypes' => $shiftTypes,
                'dprData' => [],
            ]
        );
        
        return view('pages.web.analysis.dpr', $viewData);
    }

    public function getProductions(Request $request, $plant_uid)
    {
        //list of production id with date and shift type
        $productions = [];
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $workCenters = $plant->onPlantDb()->workCenters;

        $work_center_uid = $request->work_center_uid;
        $shift_type_id = $request->shift_type_id;
        $date = $request->date;

        $workCenter = $workCenters->where('uid', '=', $work_center_uid)->first();

        $productions = $workCenter->productions()
            ->where('shift_type_id', '=', $shift_type_id)
            ->where('shift_date', '=', $date)
            ->where(Production::TABLE_NAME . '.status', '=', Production::STATUS_STOPPED)
            ->orderBy('started_at', 'asc')->select('started_at', 'id')->get()->makeHidden('runtime_summary')->toArray();

        return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK", $productions);
    }

    
    //get dprData for a production
    public function getDprData(Request $request, $plant_uid)
    {
        //wc.id , prod_id, plant_uid
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();

        $work_center_uid = $request->work_center_uid;
        $production_id = $request->production_id;

        $workCenter = $plant->onPlantDb()->workCenters()->where(WorkCenter::TABLE_NAME . '.uid', '=', $work_center_uid)->first();

        if (!$workCenter) {
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Work Center not found");
        }

        $production = $workCenter->productions()->where(Production::TABLE_NAME . '.id', '=', $production_id)->first();

        if (!$production) {
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Production not found");
        }

        return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK", AnalysisDpr::create($plant, $production));
    }

    public function exportDprData(Request $request, $plant_uid, $work_center_uid, $production_id)
    {
            //wc.id , prod_id, plant_uid
            $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
    
            $workCenter = $plant->onPlantDb()->workCenters()->where(WorkCenter::TABLE_NAME . '.uid', '=', $work_center_uid)->first();
    
            if (!$workCenter) {
                abort(404);
            }
    
            $production = $workCenter->productions()->where(Production::TABLE_NAME . '.id', '=', $production_id)->first();
    
            if (!$production) {
                abort(404);
            }
    
            return  AnalysisDpr::create($plant, $production)->exportExcel();

        
    }
}
