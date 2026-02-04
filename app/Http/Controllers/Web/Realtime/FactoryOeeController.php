<?php

namespace App\Http\Controllers\Web\Realtime;

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
        $viewData = $this->getPlantFactory($plantUid, $factoryUid);
        if (is_null($viewData))
            abort(404);

        /** @var \App\Models\Plant $plant */
        $plant = $viewData['plant'];

        $factories = $plant->onPlantDb()->factories()->with('workCenters')->get();

        if (!is_null($viewData['factory'])) {
            $viewFactories = [];
            $viewFactories[] = $viewData['factory'];
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
            $viewData,
            [
                'topBarTitle' => 'REAL TIME PRODUCTION MONITORING',
                'factories' => $factories, //factory selection list
                'viewFactories' => $viewFactories, //view Factory OEE
                'terminalsData' => $terminalsData,
            ]
        );
        return view('pages.web.realtime.factory-oee', $viewData);
    }
}
