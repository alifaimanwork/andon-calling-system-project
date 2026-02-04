<?php

namespace App\Http\Controllers\Terminal;

use App\Extras\Payloads\GenericRequestResult;
use App\Extras\Support\DieChangeInfo;
use App\Extras\Traits\TerminalRoutingTrait;
use App\Extras\Traits\WorkCenterTrait;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DieChangeController extends Controller
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

        if (!$workCenter->currentProduction) {
            //no production, reset work station state to idle
            $workCenter->status = WorkCenter::STATUS_IDLE;
            $workCenter->save();
            return redirect()->route('terminal.production-planning.index', [$plantUid, $workCenterUid]);
        }

        if ($shouldRoute = $this->checkTerminalRoute($workCenter)) {
            return $shouldRoute;
        }

        /** @var \App\Models\Production $currentProduction */
        $currentProduction = $workCenter->currentProduction;

        /** @var \App\Models\DieChangeInfo $dieChangeInfo */
        $dieChangeInfo = (new DieChangeInfo())->populate($currentProduction->die_change_info);
        if (!$dieChangeInfo)
            $dieChangeInfo = $workCenter->generateDieChangeInfoTemplate();

        $productionLines = $currentProduction->productionLines()->with(['productionOrder', 'part'])->orderBy('line_no', 'ASC')->get();

        $viewData = array_merge(
            $zoneData,
            [
                'user' => User::getCurrent(),
                'menuActive' => 'die-change',
                'topBarTitle' => 'SMI IPOS TERMINAL',
                'production' => $currentProduction,
                'productionLines' => $productionLines,
                'dieChangeInfo' => $dieChangeInfo,
                'topBarTitle' => 'SMI IPOS TERMINAL'
            ]
        );

        return view('pages.terminal.die-change.index', $viewData);
    }

    public function setCancelDieChange(Request $request, $plantUid, $workCenterUid)
    {
        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid, false);

        if (!$zoneData)
            abort(404);

        /** @var \App\Models\WorkCenter $workCenter  */
        $workCenter = $zoneData->workCenter;

        /** @var \App\Models\Plant $plant */
        $plant = $zoneData->plant;


        //Start die change!
        return $workCenter->setCancelDieChange();
        //return $zoneData;
    }
    public function setFirstProductConfirmation(Request $request, $plantUid, $workCenterUid)
    {
        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid, false);

        if (!$zoneData)
            abort(404);

        /** @var \App\Models\WorkCenter $workCenter  */
        $workCenter = $zoneData->workCenter;

        /** @var \App\Models\Plant $plant */
        $plant = $zoneData->plant;

        if (!$workCenter->currentProduction)
            abort(404);

        /** @var \App\Models\Production $production */
        $production = $workCenter->currentProduction;


        /** @var \App\Extras\Support\DieChangeInfo $dieChangeInfo */
        $dieChangeInfo = (new DieChangeInfo())->populate($production->die_change_info);
        if (!$dieChangeInfo)
            $dieChangeInfo = $workCenter->generateDieChangeInfoTemplate();

        if (Validator::make(
            $request->all(),
            [
                'lot_count' => ['required', Rule::in([$dieChangeInfo->lot_count])],
                'man_power' => ['required', 'integer', 'min:0'],
                'coil_bar' => ['required', 'array', 'size:' . $dieChangeInfo->lot_count],
                'child_part' => ['required', 'array', 'size:' . $dieChangeInfo->lot_count],
                'material_part' => ['required', 'array', 'size:' . $dieChangeInfo->lot_count],
            ]
        )->fails()) {
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid parameters");
        }

        $dieChangeInfo->man_power = $request->man_power;
        $dieChangeInfo->coil_bar = $request->coil_bar;
        $dieChangeInfo->child_part = $request->child_part;
        $dieChangeInfo->material_part = $request->material_part;

        $production->die_change_info = $dieChangeInfo;
        $production->save();

        return $workCenter->setFirstProductConfirmation();
    }
}
