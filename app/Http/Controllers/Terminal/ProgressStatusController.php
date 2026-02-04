<?php

namespace App\Http\Controllers\Terminal;

use App\Extras\Traits\TerminalRoutingTrait;
use App\Extras\Traits\WorkCenterTrait;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkCenter;
use Illuminate\Http\Request;
use App\Models\Calling;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
//use App\Events\Terminal\CallingStateChanged;

class ProgressStatusController extends Controller
{
    use WorkCenterTrait;
    use TerminalRoutingTrait;

    public function index(Request $request, $plantUid, $workCenterUid)
    {
        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid);
        if (is_null($zoneData))
            abort(404);

        $workCenter = $zoneData['workCenter'];
        
        if (!$workCenter->currentProduction) {
            $workCenter->status = WorkCenter::STATUS_IDLE;
            $workCenter->save();
            return redirect()->route('terminal.production-planning.index', [$plantUid, $workCenterUid]);
        }

        if ($shouldRoute = $this->checkTerminalRoute($workCenter)) {
            return $shouldRoute;
        }

        $currentProduction = $workCenter->currentProduction;

        if ($currentProduction)
            $productionLines = $currentProduction->productionLines()->with(['productionOrder', 'part'])->orderBy('line_no')->get();
        else
            $productionLines = [];

        $workCenterDowntimes = $workCenter->workCenterDowntimes()->get();
        if ($currentProduction)
            $activeDowntimeEvents = $currentProduction->getActiveDowntimeEvent();
        else
            $activeDowntimeEvents = [];

        $callingTimers = Calling::where('work_center_id', $workCenter->id)
                                ->where('state', 1)
                                ->get();

        $viewData = array_merge(
            $zoneData,
            [
                'user' => User::getCurrent(),
                'menuActive' => 'progress-status',
                'topBarTitle' => 'SMI IPOS TERMINAL',

                'production' => $currentProduction,
                'productionLines' => $productionLines,

                'workCenterDowntimes' => $workCenterDowntimes,
                'activeDowntimeEvents' => $activeDowntimeEvents,
                'downtimes' => $workCenter->downtimes,
                'callingTimers' => $callingTimers
            ]
        );
        
        return view('pages.terminal.progress-status.index', $viewData);
    }    

    public function setStopProduction(Request $request, $plantUid, $workCenterUid)
    {
        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid, false);

        if (!$zoneData)
            abort(404);

        /** @var \App\Models\WorkCenter $workCenter  */
        $workCenter = $zoneData->workCenter;

        /** @var \App\Models\Plant $plant */
        $plant = $zoneData->plant;

        //Stop Production
        return $workCenter->setStopProduction();
    }

    public function setResumeProduction(Request $request, $plantUid, $workCenterUid)
    {
        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid, false);

        if (!$zoneData)
            abort(404);

        /** @var \App\Models\WorkCenter $workCenter  */
        $workCenter = $zoneData->workCenter;

        /** @var \App\Models\Plant $plant */
        $plant = $zoneData->plant;

        //Resume Production
        return $workCenter->setResumeProduction();
    }

    public function setBreakProduction(Request $request, $plantUid, $workCenterUid)
    {
        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid, false);

        if (!$zoneData)
            abort(404);

        /** @var \App\Models\WorkCenter $workCenter  */
        $workCenter = $zoneData->workCenter;

        /** @var \App\Models\Plant $plant */
        $plant = $zoneData->plant;

        //Stop Production
        return $workCenter->setBreakProduction();
    }

    // New methods for starting and stopping calling timers
    public function startCallingTimer(Request $request, $plantUid, $workCenterUid, $type)
    {
        try {
            $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid);
            if (is_null($zoneData)) {
                return response()->json(['status' => 'error', 'message' => 'Work center not found'], 404);
            }

            /** @var \App\Models\WorkCenter $workCenter */
            $workCenter = $zoneData['workCenter'];

            $calling = new Calling();
            $calling->type = $type;
            $calling->start_time = Carbon::now();
            $calling->state = 1;
            $calling->work_center_id = $workCenter->id;
            $calling->work_center_uid = $workCenter->uid;

            $currentProduction = $workCenter->currentProduction;
            //Log::info('Current Production:', ['currentProduction' => $currentProduction]);

            if ($currentProduction) {
                $productionOrder = $currentProduction->productionOrders()->first();
                //Log::info('Production Order:', ['productionOrder' => $productionOrder]);

                $calling->production_order_id = $productionOrder->id ?? null;
                $calling->production_order = $productionOrder->order_no ?? null;

                $currentLine = $currentProduction->productionLines()->where('line_no', $workCenter->production_line_count)->first();
                if ($currentLine) {
                    $calling->part_id = $currentLine->part->id ?? null;
                    $calling->part_number = $currentLine->part->part_no ?? null;
                    $calling->part_name = $currentLine->part->name ?? null;
                    $calling->line_no = $currentLine->line_no ?? null;
                }

                $shift = $currentProduction->shiftType;
                if ($shift) {
                    $calling->shift_type_id = $shift->id ?? null;
                    $calling->shift_name = $shift->name ?? null;
                }
            }

            $calling->save();

            //Log::info('Calling saved:', ['calling' => $calling]);

            return response()->json(['status' => 'timer_started', 'type' => $type, 'calling_id' => $calling->id]);
        } catch (\Exception $e) {
            Log::error('Error starting calling timer: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Error starting calling timer'], 500);
        }
    }

    public function stopCallingTimer(Request $request, $plantUid, $workCenterUid, $type)
    {
        try {
            $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid);
            if (is_null($zoneData)) {
                return response()->json(['status' => 'error', 'message' => 'Work center not found'], 404);
            }
    
            /** @var \App\Models\WorkCenter $workCenter */
            $workCenter = $zoneData['workCenter'];
    
            $calling = Calling::where('work_center_id', $workCenter->id)
                ->where('type', $type)
                ->whereNull('end_time')
                ->orderBy('start_time', 'desc')
                ->first();
    
            if (!$calling) {
                return response()->json(['status' => 'error', 'message' => 'No active calling timer found'], 404);
            }
    
            $calling->end_time = Carbon::now();
            $calling->state = 0;
            $calling->save();
    
            // Broadcast the event
            //broadcast(new CallingStateChanged($calling->state));
    
            //Log::info('Calling stopped:', ['calling' => $calling]);
    
            return response()->json(['status' => 'timer_stopped', 'type' => $type, 'calling_id' => $calling->id]);
        } catch (\Exception $e) {
            Log::error('Error stopping calling timer: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Error stopping calling timer'], 500);
        }
    }
    
    // Add this method in the ProgressStatusController
    public function getCallingStates($plantUid, $workCenterUid)
    {
        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid);
        if (is_null($zoneData)) {
            return response()->json(['status' => 'error', 'message' => 'Work center not found'], 404);
        }

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $zoneData['workCenter'];

        $activeCallings = Calling::where('work_center_id', $workCenter->id)
            ->where('state', 1)
            ->get();

        return response()->json(['status' => 'success', 'data' => $activeCallings]);
    }
    
}
