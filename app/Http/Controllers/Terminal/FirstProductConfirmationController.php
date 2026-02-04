<?php

namespace App\Http\Controllers\Terminal;

use App\Extras\Payloads\GenericRequestResult;
use App\Extras\Payloads\RejectSettingData;
use App\Extras\Support\DieChangeInfo;
use App\Extras\Support\RejectSettingUpdateInfo;
use App\Extras\Traits\TerminalRoutingTrait;
use App\Extras\Traits\WorkCenterTrait;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FirstProductConfirmationController extends Controller
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

        /** @var \App\Models\Production $currentProduction */
        $currentProduction = $workCenter->currentProduction;

        if (!$workCenter->currentProduction) {
            //no production, reset work station state to idle
            $workCenter->status = WorkCenter::STATUS_IDLE;
            $workCenter->save();
            return redirect()->route('terminal.production-planning.index', [$plantUid, $workCenterUid]);
        }

        if ($shouldRoute = $this->checkTerminalRoute($workCenter)) {
            return $shouldRoute;
        }



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

            ]
        );

        return view('pages.terminal.first-product-confirmation.index', $viewData);
    }

    public function setCancelConfirmation(Request $request, $plantUid, $workCenterUid)
    {

        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid, false);

        if (!$zoneData)
            abort(404);

        /** @var \App\Models\WorkCenter $workCenter  */
        $workCenter = $zoneData->workCenter;

        /** @var \App\Models\Plant $plant */
        $plant = $zoneData->plant;


        //Cancel First Product Confirmation
        return $workCenter->setCancelFirstProductConfirmation();
    }

    public function setRejectSettings(Request $request, $plantUid, $workCenterUid)
    {

        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid, false);

        if (!$zoneData)
            abort(404);

        /** @var \App\Models\WorkCenter $workCenter  */
        $workCenter = $zoneData->workCenter;

        /** @var \App\Models\Plant $plant */
        $plant = $zoneData->plant;


        //TODO: Validate
        if (!isset($request->production_line_id, $request->maintenance_count, $request->quality_count)) {
            abort(404);
        }


        if (!is_numeric($request->maintenance_count) || !is_numeric($request->quality_count))
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid Parameter");



        $maintenanceCount = intval($request->maintenance_count);
        $qualityCount = intval($request->quality_count);
        if (
            $maintenanceCount < 0 ||
            $qualityCount < 0 ||
            ($maintenanceCount == 0 && $qualityCount == 0)
        )
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid Parameter");


        /*
        {
            "production_line_id" : <production_line_id>
            "maintenance_count": <maintenance reject count>
            "quality_count": <quality reject count>
        }
        */

        if (!$workCenter->currentProduction || $workCenter->status != WorkCenter::STATUS_FIRST_CONFIRMATION)
            abort(404); //No Production running / not in first product confirmation


        /** @var \App\Models\Production $currentProduction */
        $currentProduction = $workCenter->currentProduction;

        $rejectSettingData = new RejectSettingData($request->production_line_id, $maintenanceCount, $qualityCount);

        return $currentProduction->setRejectSettings($rejectSettingData);
    }


    public function setStartProduction(Request $request, $plantUid, $workCenterUid)
    {
        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid, false);

        if (!$zoneData)
            abort(404);

        /** @var \App\Models\WorkCenter $workCenter  */
        $workCenter = $zoneData->workCenter;

        /** @var \App\Models\Plant $plant */
        $plant = $zoneData->plant;


        //Start production
        return $workCenter->setStartProduction();
    }
}
