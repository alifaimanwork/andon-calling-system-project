<?php

namespace App\Http\Controllers\Web\Realtime;

use App\Extras\Traits\WorkCenterTrait;
use App\Http\Controllers\Controller;
use App\Models\Plant;
use Illuminate\Http\Request;

class QualityController extends Controller
{
    use WorkCenterTrait;
    //
    public function index(Request $request, $plantUid, $workCenterUid = null, $lineNo = 1)
    {
        $zoneData = $this->getPlantWorkCenterLine($plantUid, $workCenterUid, $lineNo);
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
            return view('pages.web.realtime.no-work-center.quality_no_workcenter',$zoneData);
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
        $viewData = array_merge(
            $zoneData,
            [
                'topBarTitle' => 'REAL TIME PRODUCTION MONITORING',
                'workCenters' => $workCenters,

                'production' => $currentProduction,
                'productionLines' => $productionLines,
            ]
        );
        return view('pages.web.realtime.quality', $viewData);
    }
}
