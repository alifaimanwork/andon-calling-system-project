<?php

namespace App\Http\Controllers\Web\Analysis;

use App\Extras\Payloads\GenericRequestResult;
use App\Extras\Support\AnalysisFactoryOee;
use App\Extras\Traits\FactoryTrait;
use App\Http\Controllers\Controller;
use App\Models\Plant;
use Illuminate\Http\Request;

class FactoryOeeController extends Controller
{
    use FactoryTrait;
    //
    public function index(Request $request, $plantUid, $factoryUid = null)
    {

        $zoneData = $this->getPlantFactory($plantUid, $factoryUid);
        if (is_null($zoneData))
            abort(404);

        /** @var \App\Models\Plant $plant */
        $plant = $zoneData['plant'];

        $factories = $plant->onPlantDb()->factories()->with('workCenters')->get();

        if (!is_null($zoneData['factory'])) {
            $viewFactories = [];
            $viewFactories[] = $zoneData['factory'];
        } else
            $viewFactories = $factories;

        $terminalsData = [];
        //construct terminalsData

        /** @var \App\Models\Factory $factory */
        foreach ($factories as $factory) {
            /** @var \App\Models\WorkCenter $workCenter */
            foreach ($factory->workCenters as $workCenter) {
                /** @var \App\Models\Production $pruduction */
                $production = $workCenter->currentProduction;

                if ($production)
                    $productionLines = $production->productionLines;
                else
                    $productionLines = [];

                $terminalsData[] = [
                    'plant' => $plant,
                    'workCenter' => $workCenter,
                    'production' => $production,
                    'productionLines' => $productionLines
                ];
            }
        }


        $viewData = array_merge(
            $zoneData,
            [
                'topBarTitle' => 'OPERATIONAL ANALYSIS',
                'factories' => $factories, //factory selection list
                'viewFactories' => $viewFactories, //view Factory OEE
                'terminalsData' => $terminalsData,
            ]
        );
        return view('pages.web.analysis.factory-oee', $viewData);
    }

    public function getData(Request $request, $plantUid, $factoryUid = null)
    {
        $zoneData = $this->getPlantFactory($plantUid, $factoryUid);
        if (is_null($zoneData))
            abort(404);

        /** @var \App\Models\Plant $plant */
        $plant = $zoneData['plant'];

        //TODO Validate Date


        $uids = null;
        if ($factoryUid)
            $uids = [$factoryUid];

        if ($request->format == 'print') {
            $viewData = [
                'title' => 'Operational Analysis - Factory OEE',
                'data' => AnalysisFactoryOee::create($plant, $uids, $request->date)->expandData()
            ];
            return view('pages.web.analysis.print.factory-oee', $viewData);
        } else if ($request->format == 'download') {
            return AnalysisFactoryOee::create($plant, $uids, $request->date)->export();
        } else {
            return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK", AnalysisFactoryOee::create($plant, $uids, $request->date));
        }
    }
}
