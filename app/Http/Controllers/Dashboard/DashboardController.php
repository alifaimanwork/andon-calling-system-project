<?php

// app/Http/Controllers/Dashboard/DashboardController.php

namespace App\Http\Controllers\Dashboard;

use App\Extras\Traits\WorkCenterTrait;
use App\Http\Controllers\Controller;
use App\Models\Plant;
use App\Models\WorkCenter;
use App\Models\Calling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    use WorkCenterTrait;

    public function index(Request $request, $plantUid, $workCenterUid)
    {
        $plant = Plant::where('uid', $plantUid)->firstOrFail();
        $workCenter = $plant->onPlantDb()->workCenters()->where(WorkCenter::TABLE_NAME . '.uid', $workCenterUid)->firstOrFail();

        if (!$workCenter->dashboardLayout) {
            abort(404);
        }

        $view = $workCenter->dashboardLayout->getView();

        if (!$view || !View::exists($view)) {
            abort(404);
        }

        $currentProduction = $workCenter->currentProduction;
        $productionLines = $currentProduction ? $currentProduction->productionLines()->with(['productionOrder', 'part'])->get() : [];

        $viewData = [
            'plant' => $plant,
            'workCenter' => $workCenter,
            'production' => $currentProduction,
            'productionLines' => $productionLines,
            'updateTerminalUrl' => route('dashboard.get.data', [$plant->uid, $workCenter->uid]),
            'pageTitle' => 'IPOS DASHBOARD'
        ];

        return view($view, $viewData);
    }

    public function getTerminalData(Request $request, $plantUid, $workCenterUid)
    {
        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid);
        if (is_null($zoneData)) {
            abort(404);
        }

        $plant = $zoneData['plant'];
        $workCenter = $zoneData['workCenter'];

        $responseData = [
            'plantId' => $plant->id,
            'plantUid' => $plant->uid,
            'workCenterUid' => $workCenter->uid,
        ];

        $currentProduction = $workCenter->currentProduction()->first();
        if ($currentProduction) {
            $responseData['production'] = $currentProduction->toArray();
            $responseData['productionLines'] = $currentProduction->productionLines()->with('productionOrder')->get()->toArray();
        } else {
            $responseData['production'] = null;
            $responseData['productionLines'] = [];
        }
        $responseData['workCenter'] = $workCenter->toArray();

        return $responseData;
    }

    public function loadScreen($plantUid, $workCenterUid, $screenNumber)
    {
        $plant = Plant::where('uid', $plantUid)->firstOrFail();
        $workCenter = $plant->onPlantDb()->workCenters()->where(WorkCenter::TABLE_NAME . '.uid', $workCenterUid)->firstOrFail();

        if (!$workCenter->dashboardLayout) {
            abort(404);
        }

        if ($screenNumber == 7) {
            $view = 'Pages.Dashboard.screen-7';
        } else {
            abort(404);
        }

        $currentProduction = $workCenter->currentProduction;
        $productionLines = $currentProduction ? $currentProduction->productionLines()->with(['productionOrder', 'part'])->get() : [];

        $viewData = [
            'plant' => $plant,
            'workCenter' => $workCenter,
            'production' => $currentProduction,
            'productionLines' => $productionLines,
            'updateTerminalUrl' => route('dashboard.get.data', [$plant->uid, $workCenter->uid]),
            'pageTitle' => 'IPOS DASHBOARD'
        ];

        return view($view, $viewData);
    }
    
    public function getCallingsState(Request $request, $plantUid, $workCenterUid)
    {
        $plant = Plant::where('uid', $plantUid)->firstOrFail();
        $workCenter = $plant->onPlantDb()->workCenters()->where(WorkCenter::TABLE_NAME . '.uid', $workCenterUid)->firstOrFail();

        $state = DB::table('ipos_plant.callings')
            ->where('work_center_id', $workCenter->id)
            ->pluck('state');

        // If any state is 1, return 1; otherwise, return 0
        $aggregatedState = $state->contains(1) ? 1 : 0;

        return response()->json(['state' => $aggregatedState]);
    }

    public function getCallingsStateByType(Request $request, $plantUid, $workCenterUid)
    {
        $plant = Plant::where('uid', $plantUid)->firstOrFail();
        $workCenter = $plant->onPlantDb()->workCenters()->where(WorkCenter::TABLE_NAME . '.uid', $workCenterUid)->firstOrFail();
        
        // Fetch states from the specific database
        $states = DB::table('ipos_plant.callings')
        ->select('type', 'state')
        ->get();

        // Aggregate states by type
        $aggregatedStates = [
            'leader' => 0,
            'maintenance' => 0,
            'logistic' => 0,
            'qccheck' => 0
        ];

        foreach ($states as $state) {
            if ($state->state == 1) {
                switch ($state->type) {
                    case 'leader':
                        $aggregatedStates['leader'] = 1;
                        break;
                    case 'maintenance':
                        $aggregatedStates['maintenance'] = 1;
                        break;
                    case 'logistic':
                        $aggregatedStates['logistic'] = 1;
                        break;
                    case 'qccheck':
                        $aggregatedStates['qccheck'] = 1;
                        break;
                }
            }
        }

        return response()->json($aggregatedStates);
    }

    public function getActiveCallingStates(Request $request, $plantUid, $workCenterUid)
    {
        $plant = Plant::where('uid', $plantUid)->firstOrFail();
        $workCenter = $plant->onPlantDb()->workCenters()->where(WorkCenter::TABLE_NAME . '.uid', $workCenterUid)->firstOrFail();

        $activeCallings = Calling::where('work_center_id', $workCenter->id)
            ->where('state', 1)
            ->get();

        return response()->json(['status' => 'success', 'data' => $activeCallings]);
    }
}
