<?php

namespace App\Http\Controllers\Web\Analysis;

use App\Extras\Payloads\GenericRequestResult;
use App\Extras\Support\AnalysisSummary;
use App\Extras\Traits\PlantTrait;
use App\Http\Controllers\Controller;
use App\Models\Plant;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    use PlantTrait;
    //
    public function index(Request $request, $plant_uid)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        //TODO: Check user permission for selected plant

        $viewData = [
            'topBarTitle' => 'OPERATIONAL ANALYSIS',
            'plant' => $plant
        ];
        return view('pages.web.analysis.summary', $viewData);
    }
    public function getData(Request $request, $plantUid)
    {

        $zoneData = $this->getPlant($plantUid);
        if (is_null($zoneData))
            abort(404);

        /** @var \App\Models\Plant $plant */
        $plant = $zoneData['plant'];


        //TODO Validate Date
        $dateStart = $request->date_start;
        $dateEnd = $request->date_end;

        if ($request->format == 'print') {
            return view('pages.web.analysis.print.summary', ['title' => 'Operational Analysis - Summary', 'data' => AnalysisSummary::create($plant, $dateStart, $dateEnd)]);
        } else if ($request->format == 'download') {
            return AnalysisSummary::create($plant, $dateStart, $dateEnd)->export();
        } else {
            return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK", AnalysisSummary::create($plant, $dateStart, $dateEnd));
        }
    }
}
