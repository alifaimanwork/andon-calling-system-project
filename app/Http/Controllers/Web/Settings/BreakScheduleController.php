<?php

namespace App\Http\Controllers\Web\Settings;

use App\Models\Plant;
use App\Models\BreakTime;
use Illuminate\Http\Request;
use App\Models\BreakSchedule;
use App\Http\Controllers\Controller;
use App\Extras\Datasets\BreakScheduleDataset;
use SebastianBergmann\CodeCoverage\Node\CrapIndex;
use SebastianBergmann\Environment\Console;

class BreakScheduleController extends Controller
{
    //TODO: Resource Guard
    public function index(Request $request, $plant_uid)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        //TODO: Check user permission for selected plant

        $viewData = [
            'topBarTitle' => 'PLANT SETTINGS',
            'plant' => $plant
        ];
        return view('pages.web.plant-settings.break-schedule.index', $viewData);
    }

    //create
    public function create(Request $request, $plant_uid)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();

        $viewData = [
            'topBarTitle' => 'PLANT SETTINGS',
            'plant' => $plant
        ];
        return view('pages.web.plant-settings.break-schedule.create', $viewData);
    }

    //store
    public function store(Request $request, $plant_uid)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        //check if already exists in database
        $break_schedule_name = BreakSchedule::on($plantConnection)->where('name', '=', $request->input('name'))->first();

        if ($break_schedule_name) {
            $error['name'] = 'Break Schedule with this name already exists';
            return redirect()->back()->withErrors($error)->withInput();
        }
        $break_schedule = new BreakSchedule();
        $break_schedule->plant_id = $plant->id;
        $break_schedule->name = $request->input('name');
        $break_schedule->enabled = $request->input('enabled');
        $break_schedule->setConnection($plantConnection)->save();

        //json to array 
        $break_times = json_decode($request->input('schedule_data'));

        $saved = [];
        foreach ($break_times->data as $value_day) {
            foreach ($value_day as $value) {
                $break_time = new BreakTime();
                $break_time->break_schedule_id = $break_schedule->id;
                $break_time->start_time = $value->start_time;
                $break_time->day_of_week = $value->table_id;

                $break_time->setDuration($value->end_time)
                    ->setConnection($plantConnection)
                    ->save();
                    $saved [] = $break_time;
            }
        };

        return redirect()->route('settings.break-schedule.index', $plant_uid);
    }

    //edit
    public function edit(Request $request, $plant_uid, $break_schedule_id)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        $break_schedule = BreakSchedule::on($plantConnection)->where('id', '=', $break_schedule_id)->first();

        $break_times = BreakTime::on($plantConnection)->where('break_schedule_id', '=', $break_schedule_id)->orderBy('start_time')->get();

        //BREAK TIME TO JSON
        $break_time_json = [
                'data' => [
                    '1' => [],
                    '2' => [],
                    '3' => [],
                    '4' => [],
                    '5' => [],
                    '6' => [],
                    '7' => [],
                ]
            ];

        foreach ($break_times as &$value) {
            $break_time_json['data'][$value->day_of_week][] = [
                'table_id' => $value->day_of_week,
                'start_time' => substr($value->start_time, 0, -3),
                'duration' => $value->duration,
                'end_time' => substr($value->end_time, 0, -3)
            ];
        }
        
        // fakhrul
        $breakChartData = $this->createBreakChartData($break_times);

        $viewData = [
            'topBarTitle' => 'PLANT SETTINGS',
            'plant' => $plant,
            'break' => $break_schedule,
            'schedule_data' => json_encode($break_time_json),
            'break_times' => $break_times,
            'breakChartData' => $breakChartData, // fakhrul
        ];

        return view('pages.web.plant-settings.break-schedule.edit', $viewData);
    }

    //update
    public function update(Request $request, $plant_uid, $break_schedule_id)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        $break_schedule = BreakSchedule::on($plantConnection)->where('id', '=', $break_schedule_id)->first();

        //check if already exists in database
        $break_schedule_name = BreakSchedule::on($plantConnection)->where('name', '=', $request->input('name'))->where('id', '!=', $break_schedule_id)->first();

        if ($break_schedule_name) {
            $error['name'] = 'Break Schedule with this name already exists';
            return redirect()->back()->withErrors($error)->withInput();
        }

        $break_schedule->name = $request->input('name');
        $break_schedule->enabled = $request->input('enabled');

        $break_schedule->setConnection($plantConnection)->save();


        //find and delete all break times
        BreakTime::on($plantConnection)->where('break_schedule_id', '=', $break_schedule_id)->delete();

        //json to array 
        $break_times = json_decode($request->input('schedule_data'));

        $saved = [];
        foreach ($break_times->data as $value_day) {
            foreach ($value_day as $value) {
                $break_time = new BreakTime();
                $break_time->break_schedule_id = $break_schedule->id;
                $break_time->start_time = $value->start_time;
                $break_time->day_of_week = $value->table_id;

                $break_time->setDuration($value->end_time)
                    ->setConnection($plantConnection)
                    ->save();
                    $saved [] = $break_time;
            }
        };

        return redirect()->route('settings.break-schedule.edit', [$plant_uid, $break_schedule_id]);
    }

    //TODO: break_time 
    // guna table add row
    // ada chart JS
    // left and right side
    // chart js kat atas

    //datatable
    public function datatable(Request $request, $plant_uid)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();

        $dataset = new BreakScheduleDataset();

        return $dataset->setPlant($plant)->datatable($request);
    }

    public function createBreakChartData($break_times){
        $dateofWeeks = [1,2,3,4,5,6,7];
        $breakChartData = [];
        
        foreach($dateofWeeks as $dateofWeek){
            $break_data = [];
            $remainder_data = [];

            $all_times = ['00:00:00', '24:00:00'];
            $start_times = [];
            $end_times = [];
            foreach ($break_times as $value) {
                if($value->day_of_week == $dateofWeek){
                    // $chartData[] = [
                    //     'x' => $value->start_time,
                    //     'y' => 1,
                    // ];
    
                    // $chartData[] = [
                    //     'x' => $value->end_time,
                    //     'y' => 0,
                    // ];

                    if(!in_array($value->start_time, $start_times )){
                        $start_times[] = $value->start_time;
                    }

                    if(!in_array($value->end_time, $end_times)){
                        $end_times[] = $value->end_time;
                    }

                    if(!in_array($value->start_time, $all_times)){
                        $all_times[] = $value->start_time;
                    }

                    if(!in_array($value->end_time, $all_times)){
                        $all_times[] = $value->end_time;
                    }
                }
            }

            sort($all_times);
            sort($start_times);
            sort($end_times);

            $timeline_times = [];
            $start_time = null;
            foreach ($all_times as $time) {
                
                if($start_time){
                    $timeline_times[] = [
                        'start' => $start_time,
                        'end' => $time,
                    ];
                    $start_time = $time;
                }else{
                    $start_time = $time;
                }
            }
            
            foreach($timeline_times as $timeline_time){
                $is_break = false;
                foreach($start_times as $start_time){
                    if($timeline_time['start'] == $start_time){
                        $is_break = true;
                        continue;
                    }
                }

                if($is_break){
                    $break_data[] = [
                        'x' => $timeline_time['start'],
                        'y' => 1,
                    ];

                    $break_data[] = [
                        'x' => $timeline_time['end'],
                        'y' => 0,
                    ];
                } else {
                    $remainder_data[] = [
                        'x' => $timeline_time['start'],
                        'y' => 1,
                    ];

                    $remainder_data[] = [
                        'x' => $timeline_time['end'],
                        'y' => 0,
                    ];
                }
            }

            $chartData = [
                'break' => $break_data,
                'remainder' => $remainder_data
            ];

            $breakChartData[]= [
                'day_of_week' => $dateofWeek,
                'chart_data' => $chartData,
            ];
        }
        return $breakChartData;
    }


}
