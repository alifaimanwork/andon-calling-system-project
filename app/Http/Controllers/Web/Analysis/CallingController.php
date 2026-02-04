<?php

namespace App\Http\Controllers\Web\Analysis;

use App\Extras\Payloads\GenericRequestResult;
#use App\Extras\Support\AnalysisDowntime;
use App\Extras\Traits\PlantTrait;
use App\Extras\Traits\WorkCenterTrait;
use App\Http\Controllers\Controller;
use App\Models\Plant;
use App\Models\WorkCenter;
use App\Models\Calling;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CallingController extends Controller
{
    //
    use WorkCenterTrait;
    use PlantTrait;
    //
    public function index(Request $request, $plantUid, $workCenterUid = null)
    {
        //TODO: Check user permission for selected plant
        $zoneData = $this->getPlantWorkCenter($plantUid, $workCenterUid);
        if (is_null($zoneData))
            abort(404);

        //TODO: Check user permission for selected plant

        /** @var \App\Models\Plant $plant */
        $plant = $zoneData['plant'];

        $workCenters = $plant->onPlantDb()->workCenters;

        if (count($workCenters) == 0) {
            //no workcenter in plant
            $zoneData = array_merge(
                $zoneData,
                [
                    'topBarTitle' => 'OPERATIONAL ANALYSIS',
                ]
            );

            return view('pages.web.analysis.no-work-center.calling_no_workcenter', $zoneData);
        }

        $viewData = array_merge(
            $zoneData,
            [
                'topBarTitle' => 'OPERATIONAL ANALYSIS',
                'workCenters' => $workCenters,
            ]
        );
        return view('pages.web.analysis.calling', $viewData);
    }

    public function getData(Request $request, $plantUid)
    {
        $zoneData = $this->getPlant($plantUid);
        if (is_null($zoneData)) {
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid plant UID.");
        }
    
        /** @var \App\Models\Plant $plant */
        $plant = $zoneData['plant'];
    
        $workCenterUid = $request->work_center_uid;
    
        if (!$workCenterUid) {
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid parameter.");
        }
    
        $workCenter = $plant->onPlantDb()->workCenters()->where('enabled', 1)->where(WorkCenter::TABLE_NAME . '.uid', $workCenterUid)->first();
    
        if (!$workCenter) {
            return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid parameter.");
        }
    
        // Validate Date
        $dateStart = Carbon::parse($request->date_start)->startOfDay();
        $dateEnd = Carbon::parse($request->date_end)->endOfDay();
    
        //Log::info('Date Start: ' . $dateStart);
        //Log::info('Date End: ' . $dateEnd);
    
        $callings = Calling::where('work_center_id', $workCenter->id)
            ->whereBetween('start_time', [$dateStart, $dateEnd])
            ->get();
    
        //Log::info('Raw Callings Data: ' . $callings->toJson());
    
        $callingsGrouped = $callings
            ->groupBy(function ($calling) {
                return $calling->production_order . '_' . $calling->shift_type_id . '_' . Carbon::parse($calling->start_time)->format('Y-m-d');
            })
            ->map(function ($group, $key) {
                $totalCallingTime = $group->sum(function ($calling) {
                    return Carbon::parse($calling->end_time)->diffInSeconds(Carbon::parse($calling->start_time));
                });
    
                $firstCalling = $group->first();
                return [
                    'shift_date' => Carbon::parse($firstCalling->start_time)->format('Y-m-d'),
                    'shift_type_id' => $firstCalling->shift_type_id,
                    'line_no' => $firstCalling->line_no,
                    'order_no' => $firstCalling->production_order ?? 'N/A',
                    'part_no' => $firstCalling->part_number ?? 'N/A',
                    'part_name' => $firstCalling->part_name ?? 'N/A',
                    'total_calling' => $totalCallingTime,
                    'leader_calling' => $group->where('type', 'leader')->sum(function ($calling) {
                        return Carbon::parse($calling->end_time)->diffInSeconds(Carbon::parse($calling->start_time));
                    }),
                    'maintenance_calling' => $group->where('type', 'maintenance')->sum(function ($calling) {
                        return Carbon::parse($calling->end_time)->diffInSeconds(Carbon::parse($calling->start_time));
                    }),
                    'logistic_calling' => $group->where('type', 'logistic')->sum(function ($calling) {
                        return Carbon::parse($calling->end_time)->diffInSeconds(Carbon::parse($calling->start_time));
                    }),
                    'qc_check_calling' => $group->where('type', 'qccheck')->sum(function ($calling) {
                        return Carbon::parse($calling->end_time)->diffInSeconds(Carbon::parse($calling->start_time));
                    }),
                ];
            })
            ->values()
            ->all();
    
        //Log::info('Callings Grouped: ' . json_encode($callingsGrouped));
    
        // Calculate total hours for the summary
        $summary = [
            'leader_hours' => $callings->where('type', 'leader')->sum(function ($calling) {
                return Carbon::parse($calling->end_time)->diffInSeconds(Carbon::parse($calling->start_time));
            }),
            'maintenance_hours' => $callings->where('type', 'maintenance')->sum(function ($calling) {
                return Carbon::parse($calling->end_time)->diffInSeconds(Carbon::parse($calling->start_time));
            }),
            'logistic_hours' => $callings->where('type', 'logistic')->sum(function ($calling) {
                return Carbon::parse($calling->end_time)->diffInSeconds(Carbon::parse($calling->start_time));
            }),
            'qc_check_hours' => $callings->where('type', 'qccheck')->sum(function ($calling) {
                return Carbon::parse($calling->end_time)->diffInSeconds(Carbon::parse($calling->start_time));
            })
        ];
        
        $summary = array_map(function($hours) {
            return $hours / 3600; // Convert seconds to hours
        }, $summary);              
    
        if ($request->format == 'print') {
            return view('pages.web.analysis.print.calling', ['title' => 'Operational Analysis - Calling', 'data' => $callingsGrouped, 'summary' => $summary]);
        } elseif ($request->format == 'download') {
            // Implement export logic here if needed
            return response()->json(['message' => 'Export functionality not implemented.']);
        } else {
            return response()->json(new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK", ['data' => $callingsGrouped, 'summary' => $summary]));
        }
    }            
                 
    #public function getData(Request $request, $plantUid)
    #{
        #$zoneData = $this->getPlant($plantUid);
        #if (is_null($zoneData))
            #abort(404);
        
        #/** @var \App\Models\Plant $plant */
        #$plant = $zoneData['plant'];

        #$workCenterUid = $request->work_center_uid;

        #if (!$workCenterUid)
            #return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid parameter.");

        #$workCenter = $plant->onPlantDb()->workCenters()->where('enabled', 1)->where(WorkCenter::TABLE_NAME . '.uid', $workCenterUid)->first();

        #if (!$workCenter)
            #return new GenericRequestResult(GenericRequestResult::RESULT_INVALID_PARAMETERS, "Invalid parameter.");

        //TODO Validate Date
        #$dateStart = $request->date_start;
        #$dateEnd = $request->date_end;

        #if ($request->format == 'print') {
            #return view('pages.web.analysis.print.downtime', ['title' => 'Operational Analysis - Downtime', 'data' => AnalysisDowntime::create($plant, $workCenter->uid, $dateStart, $dateEnd)]);
        #} else if ($request->format == 'download') {
            #return  AnalysisDowntime::create($plant, $workCenter->uid, $dateStart, $dateEnd)->export();
        #} else {
            #return new GenericRequestResult(GenericRequestResult::RESULT_OK, "OK", AnalysisDowntime::create($plant, $workCenter->uid, $dateStart, $dateEnd));
        #}
    #}
}
