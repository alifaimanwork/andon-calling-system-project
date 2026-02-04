<?php

namespace App\Http\Controllers\Terminal;

use App\Extras\Datasets\ProductionOrderDataset;
use App\Extras\Datasets\ProductionPlanningSheetDataset;
use App\Extras\Payloads\GenericRequestResult;
use App\Extras\Traits\TerminalRoutingTrait;
use App\Extras\Traits\WorkCenterTrait;
use App\Http\Controllers\Controller;
use App\Models\ProductionOrder;
use App\Models\ShiftType;
use App\Models\User;
use App\Models\WorkCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ProductionPlanningController extends Controller
{
    use WorkCenterTrait;
    use TerminalRoutingTrait;

    //
    public function index(Request $request, $plantUid, $workCenterUid)
    {


        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid);
        if (is_null($zoneData))
            abort(404);

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $zoneData['workCenter'];

        if ($shouldRoute = $this->checkTerminalRoute($workCenter)) {
            return $shouldRoute;
        }


        $shiftTypes = $shiftTypes = ShiftType::get();
        $currentShift = $workCenter->getCurrentShift();
        $viewData = array_merge(
            $zoneData,
            [
                'user' => User::getCurrent(),
                'menuActive' => 'production-planning',
                'topBarTitle' => 'SMI IPOS TERMINAL',

                'shiftTypes' => $shiftTypes,
                'currentShift' => $currentShift
            ]
        );

        return view('pages.terminal.production-planning.index', $viewData);
    }

    //ajax
    public function getPps(Request $request, $plantUid, $workCenterUid)
    {
        //Fetch new PPS from path, resolve Null Part & return production order status = 0 (pps)

        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid, false);

        if (!$zoneData)
            abort(404);

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $zoneData->workCenter;


        //Sync from path
        $workCenter
            ->syncPpsFromDirectory()
            ->resolvePpsPartNotFound();

        $dataset = new ProductionOrderDataset();


        $dataset->setPlant($zoneData->plant)
            //->setFilters('today', true)
            ->setFilters('status', 0) //new pps only
            ->setFilters('work_center_id', $workCenter->id);

        return $dataset->datatable($request);
    }
    public function getProductionOrder(Request $request, $plantUid, $workCenterUid)
    {
        //return production order datatable

        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid, false);

        if (!$zoneData)
            abort(404);

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $zoneData->workCenter;

        $dataset = new ProductionOrderDataset();

        $dataset->setPlant($zoneData->plant)
            ->setFilters('work_center_id', $workCenter->id);

        return $dataset->datatable($request);
    }

    public function setStartDieChange(Request $request, $plantUid, $workCenterUid)
    {
        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid, false);

        if (!$zoneData)
            abort(404);


        /** @var \App\Models\WorkCenter $workCenter  */
        $workCenter = $zoneData->workCenter;

        /** @var \App\Models\Plant $plant */
        $plant = $zoneData->plant;

        //TODO: any parameter validation?
        if (!isset($request->production_orders) || !is_array($request->production_orders)) {
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid parameters");
        }

        
        //Start die change!
        return $workCenter->startDieChange($request->production_orders, $request->forced ?? false);
        //return $zoneData;
    }
}
