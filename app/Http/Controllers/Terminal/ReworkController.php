<?php

namespace App\Http\Controllers\Terminal;

use App\Extras\Datasets\ProductionLineDataset;
use App\Extras\Datasets\ProductionOrderDataset;
use App\Extras\Payloads\GenericRequestResult;
use App\Extras\Payloads\ReworkData;
use App\Extras\Traits\WorkCenterTrait;
use App\Http\Controllers\Controller;
use App\Models\Production;
use App\Models\ProductionLine;
use App\Models\ProductionOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class ReworkController extends Controller
{
    use WorkCenterTrait;
    //
    public function index(Request $request, $plantUid, $workCenterUid)
    {
        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid);
        if (is_null($zoneData))
            abort(404);
        /** @var \App\Models\Plant $plant */
        $plant = $zoneData['plant'];
        if (!$plant)
            abort(404);

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $zoneData['workCenter'];

        if (!$workCenter)
            abort(404);

        /** @var \App\Models\Production $currentProduction */
        $currentProduction = $workCenter->currentProduction;

        $workCenterDowntimes = $workCenter->workCenterDowntimes()->get();
        if ($currentProduction)
            $activeDowntimeEvents = $currentProduction->getActiveDowntimeEvent();
        else
            $activeDowntimeEvents = [];

        $currentUser = User::getCurrent();
        if (!$currentUser)
            abort(404);

        $reworkLock = $currentUser->isTerminalRework($plantUid, $workCenterUid) && $currentUser->isTerminalOperator($plantUid, $workCenterUid);
        Session::put('rework_lock_' . $plant->id . '_' . $workCenter->id, $reworkLock);

        $viewData = array_merge(
            $zoneData,
            [
                'production' => null,
                'productionLines' => [],
                'user' => User::getCurrent(),
                'menuActive' => 'rework',
                'topBarTitle' => 'SMI IPOS TERMINAL',
                'rework_lock' => $reworkLock,
                'workCenterDowntimes' => $workCenterDowntimes,
                'activeDowntimeEvents' => $activeDowntimeEvents,
                
                'downtimes' => $workCenter->downtimes
            ]
        );

        return view('pages.terminal.rework.index', $viewData);
    }

    public function setUnlockRework(Request $request, $plantUid, $workCenterUid)
    {
        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid);

        if (!$zoneData)
            abort(404);

        /** @var \App\Models\Plant $plant */
        $plant = $zoneData['plant'];

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $zoneData['workCenter'];

        $user = User::getCurrent();
        if (!$user)
            abort(404);

        if (!isset($request->password) || !Hash::check($request->password, $user->password)) {
            return new GenericRequestResult(GenericRequestResult::RESULT_RESTRICTED, "Invalid Password");
        }

        Session::put('rework_lock_' . $plant->id . '_' . $workCenter->id, false);
        return new GenericRequestResult(GenericRequestResult::RESULT_OK, "ok");
    }
    public function getPendingRework(Request $request, $plantUid, $workCenterUid)
    {
        //return production order datatable

        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid);

        if (!$zoneData)
            abort(404);


        /** @var \App\Models\Plant $plant */
        $plant = $zoneData['plant'];

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $zoneData['workCenter'];

        $dataset = new ProductionLineDataset();

        $dataset->setPlant($plant)
            ->setFilters('work_center_id', $workCenter->id)
            ->setFilters('production_status', Production::STATUS_STOPPED)
            ->setFilters('rework_status', ProductionLine::REWORK_STATUS_OPEN);

        return $dataset->datatable($request);
    }

    public function setRework(Request $request, $plantUid, $workCenterUid)
    {
        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid, false);

        if (!$zoneData)
            abort(404);

        /** @var \App\Models\WorkCenter $workCenter  */
        $workCenter = $zoneData->workCenter;

        /** @var \App\Models\Plant $plant */
        $plant = $zoneData->plant;

        /*
        {
            production_line_id: <production_line_id>
            ok_count: <ok count>
            ng_count: <ng count>
        }
        */

        $reworkLock = session('rework_lock');
        if ($reworkLock)
            abort(403);

        if (!isset($request->production_line_id, $request->ok_count, $request->ng_count))
            abort(404); //quick check TODO: validate properly


        $reworkData = new ReworkData($request->production_line_id, $request->ok_count, $request->ng_count);

        return $workCenter->setRework($reworkData);
    }

    public function setClose(Request $request, $plantUid, $workCenterUid)
    {
        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid, false);

        if (!$zoneData)
            abort(404);

        /** @var \App\Models\WorkCenter $workCenter  */
        $workCenter = $zoneData->workCenter;

        /** @var \App\Models\Plant $plant */
        $plant = $zoneData->plant;

        $reworkLock = session('rework_lock');
        if ($reworkLock)
            abort(403);

        if (!isset($request->production_line_id))
            abort(404); //quick check TODO: validate properly

        return $workCenter->closeRework($request->production_line_id);
    }
}
