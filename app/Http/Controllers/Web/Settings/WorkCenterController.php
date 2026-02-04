<?php

namespace App\Http\Controllers\Web\Settings;

use App\Extras\Datasets\OpcActiveTagDataset;
use App\Models\User;
use App\Models\Plant;
use App\Models\OpcTag;
use App\Models\WorkCenter;
use App\Models\OpcActiveTag;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\DashboardLayout;
use App\Models\WorkCenterDowntime;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Extras\Datasets\WorkCenterDataset;
use App\Extras\Utils\ToastHelper;
use App\Models\DowntimeType;
use App\Models\OpcTagType;

class WorkCenterController extends Controller
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
        return view('pages.web.plant-settings.work-center.index', $viewData);
    }

    //create
    public function create(Request $request, $plant_uid)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        $viewData = [
            'topBarTitle' => 'PLANT SETTINGS',
            'plant' => $plant,
            'downtime_tools' => $plant->onPlantDb()->downtimes()->where('downtime_type_id', '=', '1')->get(),
            'downtime_humans' => $plant->onPlantDb()->downtimes()->where('downtime_type_id', '=', '2')->get(),
            'factories' => $plant->onPlantDb()->factories()->get(),
            'dashboard_layouts' => DashboardLayout::on($plantConnection)->get(),
            'opc_tags' => OpcActiveTag::where('plant_id', '=', $plant->id)->get(),
            'break_schedules' => $plant->onPlantDb()->breakSchedules()->where('plant_id', '=', $plant->id)->get(),
        ];
        return view('pages.web.plant-settings.work-center.create', $viewData);
    }

    //store
    public function store(Request $request, $plant_uid)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();
        //TODO: Check user permission for selected plant


        //CHECK VALIDATION
        $validatedData = $request->validate([
            'wc_code' => 'required|string|max:255|regex:/^[a-z0-9-]+$/',
            'wc_line' => 'required',
            'wc_break' => 'required',

        ]);


        //path validation
        $error_data = [];
        $error_message = [];
        // foreach request that has word "path" in it, save key and value in array
        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'path') !== false) {
                $path_key = str_replace('path_', '', $key);
                $path_value = path_readable($value);
                if ($path_value != "true")
                    $error_data[$path_key] = $path_value;
            }
        }

        //foreach error data as key and value, check if value > 0
        foreach ($error_data as $key => $value) {
            if ($value == 1)
                $error_message["path_" . $key] = "Path not found / not exist";
            if ($value == 2)
                $error_message["path_" . $key] = "Path not readable";
            if ($value == 3)
                $error_message["path_" . $key] = "Path not writable";
            if ($value == 4)
                $error_message["path_" . $key] = "Not enough permission / permission denied";
        }

        //downtime validation
        $data_downtimes = json_decode($request->selectedDowntimes, true);
        //for each data_downtimes, check opc_tag_id for uniqueness
        $opc_tag_ids = [];
        foreach ($data_downtimes as $downtime) {
            if (in_array($downtime['opc_tag_id'], $opc_tag_ids) && $downtime['opc_tag_id'] != 0) {
                $error_message['opc_tag_error'] = 'Opc Tag ID must be unique.';
            } else {
                if ($downtime['opc_tag_id'] != null) {
                    array_push($opc_tag_ids, $downtime['opc_tag_id']);
                }
            }
        }

        //part_numbers validation
        $data_part_numbers = json_decode($request->selectedPartNumbers, true);
        //for each data_downtimes, check tag_id for uniqueness
        $partnumber_tag_ids_raw = [];
        foreach ($data_part_numbers as $part_number) {
            if (in_array($part_number['tag_id'], $partnumber_tag_ids_raw) && $part_number['tag_id'] != 0) {
                $error_message['part_number_tag'] = 'Part Number Tag must be unique.';
            } else {
                if ($part_number['tag_id'] != null) {
                    array_push($partnumber_tag_ids_raw, $part_number['tag_id']);
                }
            }
        }

        //count_up validation
        $data_count_ups = json_decode($request->selectedCountUps, true);
        //for each data_downtimes, check tag_id for uniqueness
        $count_up_tag_ids_raw = [];
        foreach ($data_count_ups as $count_up) {
            if (in_array($count_up['tag_id'], $count_up_tag_ids_raw) && $count_up['tag_id'] != 0) {
                $error_message['count_up_tag'] = 'Count Up Tag must be unique.';
            } else {
                if ($count_up['tag_id'] != null) {
                    array_push($count_up_tag_ids_raw, $count_up['tag_id']);
                }
            }
        }

        //add line_row to array partnumbrs
        $partnumber_tag_ids = [];
        //for with indexing
        for ($i = 0; $i < count($partnumber_tag_ids_raw); $i++) {
            $partnumber_tag_ids[$i]['tag_id'] = $partnumber_tag_ids_raw[$i];
            $partnumber_tag_ids[$i]['line_row'] = $i + 1;
        }

        //add line_row to array count_ups
        $count_up_tag_ids = [];
        //for with indexing
        for ($i = 0; $i < count($count_up_tag_ids_raw); $i++) {
            $count_up_tag_ids[$i]['tag_id'] = $count_up_tag_ids_raw[$i];
            $count_up_tag_ids[$i]['line_row'] = $i + 1;
        }

        $active_opc_data_die_change = OpcActiveTag::where('plant_id', '=', $plant->id)->where('id', '=', $request->die_change_tag)->first();
        $active_opc_data_break_tag = OpcActiveTag::where('plant_id', '=', $plant->id)->where('id', '=', $request->break_tag)->first();

        $active_opc_data_human_downtime_tag = OpcActiveTag::where('plant_id', '=', $plant->id)->where('id', '=', $request->human_downtime_tag)->first();
        $active_opc_data_on_production_tag = OpcActiveTag::where('plant_id', '=', $plant->id)->where('id', '=', $request->on_production_tag)->first();

        if ($active_opc_data_die_change == null && $request->die_change_tag != 0) {
            $error_message['die_change_tag'] = 'Die Change Tag not found.';
        }

        if ($active_opc_data_break_tag == null && $request->break_tag != 0) {
            $error_message['break_tag'] = 'Break Tag not found.';
        }

        if (!$active_opc_data_human_downtime_tag && $request->human_downtime_tag != 0) {
            $error_message['human_downtime_tag'] = 'Human Downtime Tag not found.';
        }

        if (!$active_opc_data_on_production_tag && $request->on_production_tag != 0) {
            $error_message['on_production_tag'] = 'On Production Tag not found.';
        }

        //SAVE DATA IF NO ERROR
        if (count($error_message) > 0) {

            return redirect()->back()->withErrors($error_message)->withInput();
        } else {
            $newWorkCenter = new WorkCenter();
            $newWorkCenter->plant_id = $plant->id;
            $newWorkCenter->factory_id = $request->input('wc_factory_id');
            $newWorkCenter->dashboard_layout_id = DashboardLayout::on($plantConnection)->where('capacity', '=', $request->input('wc_line'))->first()->id;
            $newWorkCenter->production_line_count = $request->input('wc_line');
            $newWorkCenter->name = $request->input('wc_name');
            $newWorkCenter->uid = $request->input('wc_code');
            $newWorkCenter->break_schedule_id = $request->input('wc_break');

            $newWorkCenter->enabled = $request->input('enabled');
            $newWorkCenter->threshold_oee = $request->input('threshold_oee_target') / 100;
            $newWorkCenter->threshold_availability = $request->input('threshold_availability_target') / 100;
            $newWorkCenter->threshold_performance = $request->input('threshold_performance_target') / 100;
            $newWorkCenter->threshold_quality = $request->input('threshold_quality_target') / 100;

            $newWorkCenter->pps_source = $request->input('path_pps');
            $newWorkCenter->gr_ok_destination = $request->input('path_gr_ok');
            $newWorkCenter->gr_ng_destination = $request->input('path_gr_ng');
            $newWorkCenter->ett10_destination = $request->input('path_ett10');
            $newWorkCenter->ett20_destination = $request->input('path_ett20');

            $newWorkCenter->rw_ng_destination = $request->input('path_rw_ng');
            $newWorkCenter->rw_ok_destination = $request->input('path_rw_ok');
            $newWorkCenter->gr_qi_destination = $request->input('path_gr_qi');
            $newWorkCenter->setConnection($plantConnection)->save();


            //save die_change_tag
            if ($request->die_change_tag != 0) {
                $newDieChangeTag = new OpcTag();
                $newDieChangeTag->plant_id = $plant->id;
                $newDieChangeTag->work_center_id = $newWorkCenter->id;
                $newDieChangeTag->opc_tag_type_id = OpcTagType::TAG_DIE_CHANGE;
                $newDieChangeTag->opc_server_id = $active_opc_data_die_change->opc_server_id;
                $newDieChangeTag->tag = $active_opc_data_die_change->tag;

                $newDieChangeTag->setConnection($plantConnection)->save();
            }

            //save break_tag
            if ($request->break_tag != 0) {
                $newBreakTag = new OpcTag();
                $newBreakTag->plant_id = $plant->id;
                $newBreakTag->work_center_id = $newWorkCenter->id;
                $newBreakTag->opc_tag_type_id = OpcTagType::TAG_BREAK;
                $newBreakTag->opc_server_id = $active_opc_data_break_tag->opc_server_id;
                $newBreakTag->tag = $active_opc_data_break_tag->tag;

                $newBreakTag->setConnection($plantConnection)->save();
            }

            //save human downtime tag
            if ($active_opc_data_human_downtime_tag) {
                $newHumanDowntime = new OpcTag();
                $newHumanDowntime->plant_id = $plant->id;
                $newHumanDowntime->work_center_id = $newWorkCenter->id;
                $newHumanDowntime->opc_tag_type_id = OpcTagType::TAG_HUMAN_DOWNTIME;
                $newHumanDowntime->opc_server_id = $active_opc_data_human_downtime_tag->opc_server_id;
                $newHumanDowntime->tag = $active_opc_data_human_downtime_tag->tag;

                $newHumanDowntime->setConnection($plantConnection)->save();
            }

            //save on production tag
            if ($active_opc_data_on_production_tag) {
                $newOnProductionTag = new OpcTag();
                $newOnProductionTag->plant_id = $plant->id;
                $newOnProductionTag->work_center_id = $newWorkCenter->id;
                $newOnProductionTag->opc_tag_type_id = OpcTagType::TAG_ON_PRODUCTION;
                $newOnProductionTag->opc_server_id = $active_opc_data_on_production_tag->opc_server_id;
                $newOnProductionTag->tag = $active_opc_data_on_production_tag->tag;

                $newOnProductionTag->setConnection($plantConnection)->save();
            }


            //save downtime - opc tag
            foreach ($data_downtimes as $downtime) {
                if ($downtime['opc_tag_id'] != null && $downtime['opc_tag_id'] != 0) {
                    $this_opc_data = OpcActiveTag::where('plant_id', '=', $plant->id)->where('id', '=', $downtime['opc_tag_id'])->first();

                    $new_OPC_tag = new OpcTag();
                    $new_OPC_tag->plant_id = $plant->id;
                    $new_OPC_tag->work_center_id = $newWorkCenter->id;
                    $new_OPC_tag->opc_tag_type_id = 5;
                    $new_OPC_tag->opc_server_id = $this_opc_data->opc_server_id;
                    $new_OPC_tag->tag = $this_opc_data->tag;

                    $new_OPC_tag->setConnection($plantConnection)->save();

                    $newWCDowntime = new WorkCenterDowntime();
                    $newWCDowntime->work_center_id = $newWorkCenter->id;
                    $newWCDowntime->downtime_id = $downtime['downtime_id'];
                    $newWCDowntime->opc_tag_id = $new_OPC_tag->id;
                    $newWCDowntime->setConnection($plantConnection)->save();
                } else {
                    $newWCDowntime = new WorkCenterDowntime();
                    $newWCDowntime->work_center_id = $newWorkCenter->id;
                    $newWCDowntime->downtime_id = $downtime['downtime_id'];
                    $newWCDowntime->opc_tag_id = null;
                    $newWCDowntime->setConnection($plantConnection)->save();
                }
            }

            //save part_number
            foreach ($partnumber_tag_ids as $part_number) {

                if ($part_number['tag_id'] != 0) {
                    $this_opc_data = OpcActiveTag::where('plant_id', '=', $plant->id)->where('id', '=', $part_number['tag_id'])->first();
                    $newPartTag = new OpcTag();
                    $newPartTag->plant_id = $plant->id;
                    $newPartTag->work_center_id = $newWorkCenter->id;
                    $newPartTag->opc_tag_type_id = 3;
                    $newPartTag->opc_server_id = $this_opc_data->opc_server_id;
                    $newPartTag->tag = $this_opc_data->tag;
                    $newPartTag->info = $part_number['line_row'];

                    $newPartTag->setConnection($plantConnection)->save();
                }
            }

            //save counter_tag
            foreach ($count_up_tag_ids as $counter_tag) {

                if ($counter_tag['tag_id'] != 0) {
                    $this_opc_data = OpcActiveTag::where('plant_id', '=', $plant->id)->where('id', '=', $counter_tag['tag_id'])->first();
                    $newCounterTag = new OpcTag();
                    $newCounterTag->plant_id = $plant->id;
                    $newCounterTag->work_center_id = $newWorkCenter->id;
                    $newCounterTag->opc_tag_type_id = 4;
                    $newCounterTag->opc_server_id = $this_opc_data->opc_server_id;
                    $newCounterTag->tag = $this_opc_data->tag;
                    $newCounterTag->info = $counter_tag['line_row'];

                    $newCounterTag->setConnection($plantConnection)->save();
                }
            }
            $newWorkCenter->updateOpcTagCacheValues();
            return redirect()->route('settings.work-center.index', ['plant_uid' => $plant->uid]);
        }
    }

    //edit
    public function edit(Request $request, $plant_uid, $work_center_uid)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();


        $workCenter = $plant->onPlantDb()->workCenters()->where('id', '=', $work_center_uid)->firstOrFail();

        // dd($workCenter, $plant);

        $partNumbersIds_raw = $plant->onPlantDb()->opcTags()->where('work_center_id', '=', $work_center_uid)->where('opc_tag_type_id', '=', 3)->get();

        $partNumbersIds = [];
        for ($i = 0; $i < count($partNumbersIds_raw); $i++) {
            $partNumbersIds[$i]['tag_id'] = $partNumbersIds_raw[$i]->tag;
            $partNumbersIds[$i]['line_row'] = $partNumbersIds_raw[$i]->info;
        }
        $partNumbersIds = json_encode($partNumbersIds);

        $countUpIds_raw = $plant->onPlantDb()->opcTags()->where('work_center_id', '=', $work_center_uid)->where('opc_tag_type_id', '=', 4)->get();
        $countUpIds = [];
        for ($i = 0; $i < count($countUpIds_raw); $i++) {
            $countUpIds[$i]['tag_id'] = $countUpIds_raw[$i]->tag;
            $countUpIds[$i]['line_row'] = $countUpIds_raw[$i]->info;
        }
        $countUpIds = json_encode($countUpIds);

        // dd($countUpIds_raw, $partNumbersIds_raw);
        $users = DB::connection($plant->getPlantConnection())
            ->table(User::TABLE_NAME)
            ->select([User::TABLE_NAME . '.*', 'user_work_center.terminal_permission'])
            ->leftJoin('user_work_center', function ($query) use ($workCenter) {
                $query->on('user_work_center.user_id', '=', User::TABLE_NAME . '.id');
                $query->on('user_work_center.work_center_id', '=', DB::raw('"' . $workCenter->id . '"'));
            })->get();

        $filtered_users = [];
        foreach ($users as $user) {
            $selected_user = User::find($user->id);
            if ($selected_user->plants()->where('terminal_permission', 1)->exists()) {
                if ($user->role == User::ROLE_USER) {
                    $filtered_users[] = $user;
                }
            }
        }

        $viewData = [
            'topBarTitle' => 'PLANT SETTINGS',
            'plant' => $plant,
            'workCenter' => $workCenter,
            'downtimes' => $plant->onPlantDb()->downtimes()->get(),
            'downtime_tools' => $plant->onPlantDb()->downtimes()->where('downtime_type_id', '=', DowntimeType::MACHINE_DOWNTIME)->get(),
            'downtime_humans' => $plant->onPlantDb()->downtimes()->where('downtime_type_id', '=', DowntimeType::HUMAN_DOWNTIME)->get(),
            'factories' => $plant->onPlantDb()->factories()->get(),
            'dashboard_layouts' => DashboardLayout::on($plantConnection)->get(),
            'opc_tags' => OpcActiveTag::where('plant_id', '=', $plant->id)->get(),
            'selected_downtimes' => $workCenter->workCenterDowntimes()->with('opcTag', 'downtime')->get(),
            'break_schedules' => $plant->onPlantDb()->breakSchedules()->where('plant_id', '=', $plant->id)->get(),

            'DBdie_change_tag' => $plant->onPlantDb()->opcTags()->where('work_center_id', '=', $work_center_uid)->where('opc_tag_type_id', '=', OpcTagType::TAG_DIE_CHANGE)->first(),
            'DBbreak_tag' => $plant->onPlantDb()->opcTags()->where('work_center_id', '=', $work_center_uid)->where('opc_tag_type_id', '=', OpcTagType::TAG_BREAK)->first(),

            'DBhuman_downtime_tag' => $plant->onPlantDb()->opcTags()->where('work_center_id', '=', $work_center_uid)->where('opc_tag_type_id', '=', OpcTagType::TAG_HUMAN_DOWNTIME)->first(),
            'DBon_production_tag' => $plant->onPlantDb()->opcTags()->where('work_center_id', '=', $work_center_uid)->where('opc_tag_type_id', '=', OpcTagType::TAG_ON_PRODUCTION)->first(),

            'DBselectedPartNumbers' => $partNumbersIds,
            'DBselectedCountUps' => $countUpIds,
            'users' => $filtered_users,
        ];


        return view('pages.web.plant-settings.work-center.edit', $viewData);
    }

    //update
    public function update(Request $request, $plant_uid, $work_center_id)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        $workCenter = $plant->onPlantDb()->workCenters()->where('plant_id', $plant->id)->where('id', '=', $work_center_id)->firstOrFail();

        //CHECK VALIDATION
        $validatedData = $request->validate([
            'wc_code' => 'required|string|max:255|regex:/^[a-z0-9-]+$/',
            'wc_line' => 'required',
            'wc_break' => 'required',

        ]);

        //path validation
        $error_data = [];
        $error_message = [];
        // foreach request that has word "path" in it, save key and value in array
        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'path') !== false) {
                $path_key = str_replace('path_', '', $key);
                $path_value = path_readable($value);
                if ($path_value != "true")
                    $error_data[$path_key] = $path_value;
            }
        }

        //foreach error data as key and value, check if value > 0
        foreach ($error_data as $key => $value) {
            if ($value == 1)
                $error_message["path_" . $key] = "Path not found / not exist";
            if ($value == 2)
                $error_message["path_" . $key] = "Path not readable";
            if ($value == 3)
                $error_message["path_" . $key] = "Path not writable";
            if ($value == 4)
                $error_message["path_" . $key] = "Not enough permission / permission denied";
        }

        //downtime validation
        $data_downtimes = json_decode($request->selectedDowntimes, true);
        //for each data_downtimes, check opc_tag_id for uniqueness
        $data_downtimes_raw = [];

        foreach ($data_downtimes as $downtime) {
            if (in_array($downtime['opc_tag_id'], $data_downtimes_raw) && $downtime['opc_tag_id'] != 0) {
                $error_message['opc_tag_error'] = 'Opc Tag ID must be unique.';
            } else {
                if ($downtime['opc_tag_id'] != null) { //for machine downtime, opc_tag_id has value
                    array_push($data_downtimes_raw, $downtime['opc_tag_id']);
                }
            }
        }

        //part_numbers validation
        $data_part_numbers = json_decode($request->selectedPartNumbers, true);
        //for each data_downtimes, check tag_id for uniqueness
        $partnumber_tag_ids_raw = [];
        foreach ($data_part_numbers as $part_number) {
            if (in_array($part_number['tag_id'], $partnumber_tag_ids_raw) && $part_number['tag_id'] != 0) {
                $error_message['part_number_tag'] = 'Part Number Tag must be unique.';
            } else {
                if ($part_number['tag_id'] != null) {
                    array_push($partnumber_tag_ids_raw, $part_number['tag_id']);
                }
            }
        }

        //count_up validation
        $data_count_ups = json_decode($request->selectedCountUps, true);
        //for each data_downtimes, check tag_id for uniqueness
        $count_up_tag_ids_raw = [];
        foreach ($data_count_ups as $count_up) {
            if (in_array($count_up['tag_id'], $count_up_tag_ids_raw) && $count_up['tag_id'] != 0) {
                $error_message['count_up_tag'] = 'Count Up Tag must be unique.';
            } else {
                if ($count_up['tag_id'] != null) {
                    array_push($count_up_tag_ids_raw, $count_up['tag_id']);
                }
            }
        }


        //add line_row to array partnumbrs
        $partnumber_tag_ids = [];
        //for with indexing
        for ($i = 0; $i < count($partnumber_tag_ids_raw); $i++) {
            $partnumber_tag_ids[$i]['tag_id'] = $partnumber_tag_ids_raw[$i];
            $partnumber_tag_ids[$i]['line_row'] = $i + 1;
        }

        //add line_row to array count_ups
        $count_up_tag_ids = [];
        //for with indexing
        for ($i = 0; $i < count($count_up_tag_ids_raw); $i++) {
            $count_up_tag_ids[$i]['tag_id'] = $count_up_tag_ids_raw[$i];
            $count_up_tag_ids[$i]['line_row'] = $i + 1;
        }

        $active_opc_data_die_change = OpcActiveTag::where('plant_id', '=', $plant->id)->where('id', '=', $request->die_change_tag)->first();
        $active_opc_data_break_tag = OpcActiveTag::where('plant_id', '=', $plant->id)->where('id', '=', $request->break_tag)->first();

        $active_opc_data_human_downtime_tag = OpcActiveTag::where('plant_id', '=', $plant->id)->where('id', '=', $request->human_downtime_tag)->first();
        $active_opc_data_on_production_tag = OpcActiveTag::where('plant_id', '=', $plant->id)->where('id', '=', $request->on_production_tag)->first();

        if (!$active_opc_data_die_change && $request->die_change_tag != 0) {
            $error_message['die_change_tag'] = 'Die Change Tag not found.';
        }

        if (!$active_opc_data_break_tag && $request->break_tag != 0) {
            $error_message['break_tag'] = 'Break Tag not found.';
        }

        if (!$active_opc_data_human_downtime_tag && $request->human_downtime_tag != 0) {
            $error_message['human_downtime_tag'] = 'Human Downtime Tag not found.';
        }

        if (!$active_opc_data_on_production_tag && $request->on_production_tag != 0) {
            $error_message['on_production_tag'] = 'On Production Tag not found.';
        }

        $user_op = $request->access_operation; //list of work centers id
        $user_rework = $request->access_rework; //list of work centers id

        $workcenter_access = []; // id => permission (0: null, 1: operation, 2: rework, 3: both)

        if ($request->has('access_operation')) {
            foreach ($user_op as $wcu_id) {
                $workcenter_access[$wcu_id] = 1;
            }
        }
        if ($request->has('access_rework')) {
            foreach ($user_rework as $wcu_id) {
                if (!isset($workcenter_access[$wcu_id]))
                    $workcenter_access[$wcu_id] = 2;
                else
                    $workcenter_access[$wcu_id] = 3;
            }
        }


        //SAVE DATA IF NO ERROR
        if (count($error_message) > 0) {

            return redirect()->back()->withErrors($error_message)->withInput();
        } else {

            $workCenter->factory_id = $request->input('wc_factory_id');
            $workCenter->dashboard_layout_id = DashboardLayout::on($plantConnection)->where('capacity', '=', $request->input('wc_line'))->first()->id;
            $workCenter->production_line_count = $request->input('wc_line');
            $workCenter->name = $request->input('wc_name');
            $workCenter->uid = $request->input('wc_code');
            $workCenter->break_schedule_id = $request->input('wc_break');

            $workCenter->enabled = $request->input('enabled');
            $workCenter->threshold_oee = $request->input('threshold_oee_target') / 100;
            $workCenter->threshold_availability = $request->input('threshold_availability_target') / 100;
            $workCenter->threshold_performance = $request->input('threshold_performance_target') / 100;
            $workCenter->threshold_quality = $request->input('threshold_quality_target') / 100;

            $workCenter->pps_source = $request->input('path_pps');
            $workCenter->gr_ok_destination = $request->input('path_gr_ok');
            $workCenter->gr_ng_destination = $request->input('path_gr_ng');
            $workCenter->ett10_destination = $request->input('path_ett10');
            $workCenter->ett20_destination = $request->input('path_ett20');

            $workCenter->rw_ng_destination = $request->input('path_rw_ng');
            $workCenter->rw_ok_destination = $request->input('path_rw_ok');
            $workCenter->gr_qi_destination = $request->input('path_gr_qi');
            $workCenter->setConnection($plantConnection)->save();

            $workCenter->releaseOpcActiveTagsSetValue();
            
            //find and delete all diechangetag that exist in OPCTag
            $deleteDieChangeTag_inDB = OpcTag::on($plantConnection)->where('work_center_id', '=', $work_center_id)->where('opc_tag_type_id', '=', OpcTagType::TAG_DIE_CHANGE)->delete();

            //save die_change_tag
            if ($request->die_change_tag != 0) {
                $newDieChangeTag = new OpcTag();
                $newDieChangeTag->plant_id = $plant->id;
                $newDieChangeTag->work_center_id = $workCenter->id;
                $newDieChangeTag->opc_tag_type_id = 1;
                $newDieChangeTag->opc_server_id = $active_opc_data_die_change->opc_server_id;
                $newDieChangeTag->tag = $active_opc_data_die_change->tag;

                $newDieChangeTag->setConnection($plantConnection)->save();
            }


            //find and delete all breaktag that exist in OPCTag
            $deleteBreakTag_inDB = OpcTag::on($plantConnection)->where('work_center_id', '=', $work_center_id)->where('opc_tag_type_id', '=', OpcTagType::TAG_BREAK)->delete();

            //save break_tag
            if ($request->break_tag != 0) {
                $newBreakTag = new OpcTag();
                $newBreakTag->plant_id = $plant->id;
                $newBreakTag->work_center_id = $workCenter->id;
                $newBreakTag->opc_tag_type_id = OpcTagType::TAG_BREAK;
                $newBreakTag->opc_server_id = $active_opc_data_break_tag->opc_server_id;
                $newBreakTag->tag = $active_opc_data_break_tag->tag;

                $newBreakTag->setConnection($plantConnection)->save();
            }

            //find and delete all human downtime that exist in OPCTag
            $deleteBreakTag_inDB = OpcTag::on($plantConnection)->where('work_center_id', '=', $work_center_id)->where('opc_tag_type_id', '=', OpcTagType::TAG_HUMAN_DOWNTIME)->delete();

            //save human downtime tag
            if ($active_opc_data_human_downtime_tag) {
                $newHumanDowntime = new OpcTag();
                $newHumanDowntime->plant_id = $plant->id;
                $newHumanDowntime->work_center_id = $workCenter->id;
                $newHumanDowntime->opc_tag_type_id = OpcTagType::TAG_HUMAN_DOWNTIME;
                $newHumanDowntime->opc_server_id = $active_opc_data_human_downtime_tag->opc_server_id;
                $newHumanDowntime->tag = $active_opc_data_human_downtime_tag->tag;

                $newHumanDowntime->setConnection($plantConnection)->save();
            }

            //find and delete all human downtime that exist in OPCTag
            $deleteBreakTag_inDB = OpcTag::on($plantConnection)->where('work_center_id', '=', $work_center_id)->where('opc_tag_type_id', '=', OpcTagType::TAG_ON_PRODUCTION)->delete();

            //save on production tag
            if ($active_opc_data_on_production_tag) {
                $newOnProductionTag = new OpcTag();
                $newOnProductionTag->plant_id = $plant->id;
                $newOnProductionTag->work_center_id = $workCenter->id;
                $newOnProductionTag->opc_tag_type_id = OpcTagType::TAG_ON_PRODUCTION;
                $newOnProductionTag->opc_server_id = $active_opc_data_on_production_tag->opc_server_id;
                $newOnProductionTag->tag = $active_opc_data_on_production_tag->tag;

                $newOnProductionTag->setConnection($plantConnection)->save();
            }


            //find and delete all data_downtimes that exist in OPCTag
            $deleteDataDowntime_inDB = OpcTag::on($plantConnection)->where('work_center_id', '=', $work_center_id)->where('opc_tag_type_id', '=', OpcTagType::TAG_DOWNTIME)->delete();
            $deleteWorkCenterDowntime_inDB = WorkCenterDowntime::on($plantConnection)->where('work_center_id', '=', $work_center_id)->delete();

            //save downtime - opc tag
            foreach ($data_downtimes as $downtime) {
                //TODO: Check if opc tag exist in database
                if ($downtime['opc_tag_id'] != null && $downtime['opc_tag_id'] != 0) { //human downtime == null
                    $this_opc_data = OpcActiveTag::where('plant_id', '=', $plant->id)->where('id', '=', $downtime['opc_tag_id'])->first();

                    $new_OPC_tag = new OpcTag();
                    $new_OPC_tag->plant_id = $plant->id;
                    $new_OPC_tag->work_center_id = $workCenter->id;
                    $new_OPC_tag->opc_tag_type_id = OpcTagType::TAG_DOWNTIME;
                    $new_OPC_tag->opc_server_id = $this_opc_data->opc_server_id;
                    $new_OPC_tag->tag = $this_opc_data->tag;

                    $new_OPC_tag->setConnection($plantConnection)->save();

                    $newWCDowntime = new WorkCenterDowntime();
                    $newWCDowntime->work_center_id = $workCenter->id;
                    $newWCDowntime->downtime_id = $downtime['downtime_id'];
                    $newWCDowntime->opc_tag_id = $new_OPC_tag->id;
                    $newWCDowntime->setConnection($plantConnection)->save();
                } else {
                    $newWCDowntime = new WorkCenterDowntime();
                    $newWCDowntime->work_center_id = $workCenter->id;
                    $newWCDowntime->downtime_id = $downtime['downtime_id'];
                    $newWCDowntime->opc_tag_id = null;
                    $newWCDowntime->setConnection($plantConnection)->save();
                }
            }

            //find and delete all partnumber_tag_ids that exist in OPCTag
            $deletePartNumberTag_inDB = OpcTag::on($plantConnection)->where('work_center_id', '=', $work_center_id)->where('opc_tag_type_id', '=', OpcTagType::TAG_PART_NUMBER)->delete();

            //save part_number
            foreach ($partnumber_tag_ids as $part_number) {

                if ($part_number['tag_id'] != 0) {
                    $this_opc_data = OpcActiveTag::where('plant_id', '=', $plant->id)->where('id', '=', $part_number['tag_id'])->first();
                    $newPartTag = new OpcTag();
                    $newPartTag->plant_id = $plant->id;
                    $newPartTag->work_center_id = $workCenter->id;
                    $newPartTag->opc_tag_type_id = OpcTagType::TAG_PART_NUMBER;
                    $newPartTag->opc_server_id = $this_opc_data->opc_server_id;
                    $newPartTag->tag = $this_opc_data->tag;
                    $newPartTag->info = $part_number['line_row'];

                    $newPartTag->setConnection($plantConnection)->save();
                }
            }

            //find and delete all count_up_tag_ids that exist in OPCTag
            $deleteCountUpTag_inDB = OpcTag::on($plantConnection)->where('work_center_id', '=', $work_center_id)->where('opc_tag_type_id', '=', OpcTagType::TAG_COUNTER)->delete();

            //save counter_tag
            foreach ($count_up_tag_ids as $counter_tag) {

                if ($counter_tag['tag_id'] != 0) {
                    $this_opc_data = OpcActiveTag::where('plant_id', '=', $plant->id)->where('id', '=', $counter_tag['tag_id'])->first();
                    $newCounterTag = new OpcTag();
                    $newCounterTag->plant_id = $plant->id;
                    $newCounterTag->work_center_id = $workCenter->id;
                    $newCounterTag->opc_tag_type_id = OpcTagType::TAG_COUNTER;
                    $newCounterTag->opc_server_id = $this_opc_data->opc_server_id;
                    $newCounterTag->tag = $this_opc_data->tag;
                    $newCounterTag->info = $counter_tag['line_row'];

                    $newCounterTag->setConnection($plantConnection)->save();
                }
            }

            //workcenter_access update
            // DB::connection($plantConnection)->table('user_work_center')
            //     ->where('work_center_id', '=', $work_center_id)
            //     ->delete();

            // foreach ($workcenter_access as $key => $value) {
            //     $workCenter->setConnection($plantConnection)->users()->attach($work_center_id, ['user_id' => $key, 'terminal_permission' => $value, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            // }

            $workCenter->updateOpcTagCacheValues();
            return redirect()->route('settings.work-center.index', ['plant_uid' => $plant->uid]);
        }
    }

    //delete
    public function destroy(Request $request, $plant_uid, $work_center_id)
    {

        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $plant->onPlantDb()->workCenters()->where('id', '=', $work_center_id)->firstOrFail();

        //TODO: Delete foreign key linked table

        if (!$workCenter->isDestroyable()) {
            ToastHelper::addToast('Unable to delete ' . $workCenter->name . '.', 'Delete Work Center', 'danger');
            return redirect()->route('settings.work-center.index', ['plant_uid' => $plant->uid]);
        }

        $workCenter->setConnection($plantConnection)->delete();

        return redirect()->route('settings.work-center.index', ['plant_uid' => $plant->uid]);
    }


    //datalist
    public function datatable(Request $request, $plant_uid)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();

        $dataset = new WorkCenterDataset();

        return $dataset->setPlant($plant)->datatable($request);
    }

    //opc tags datatable
    public function opcTagDatatable(Request $request, $plant_uid)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();

        $dataset = new OpcActiveTagDataset();
        $dataset->setFilters('plant_id', $plant->id);
        $dataset->customSelect([DB::raw('NULL as _assigned'), DB::raw('NULL as _assigned_type'), OpcActiveTag::TABLE_NAME . '.opc_server_id', OpcActiveTag::TABLE_NAME . '.tag']);
        $datatable = $dataset->datatable($request);

        $assignedTags =  DB::connection($plant->onPlantDb()->getPlantConnection())
            ->table(OpcTag::TABLE_NAME)
            ->join(WorkCenter::TABLE_NAME, OpcTag::TABLE_NAME . '.work_center_id', '=', WorkCenter::TABLE_NAME . '.id')
            ->join(OpcTagType::TABLE_NAME, OpcTag::TABLE_NAME . '.opc_tag_type_id', '=', OpcTagType::TABLE_NAME . '.id')
            ->where(OpcTag::TABLE_NAME . '.plant_id', $plant->id)
            ->select([
                DB::raw(WorkCenter::TABLE_NAME . '.name as work_center_name'),
                DB::raw(OpcTagType::TABLE_NAME . '.name as opc_tag_type_name'),
                OpcTag::TABLE_NAME . '.work_center_id',
                OpcTag::TABLE_NAME . '.opc_server_id',
                OpcTag::TABLE_NAME . '.tag'
            ])
            ->get();

        foreach ($datatable['data'] as &$row) {
            foreach ($assignedTags as $assignedTag) {
                if ($row->tag == $assignedTag->tag && $row->opc_server_id == $assignedTag->opc_server_id) {
                    $row->_assigned = $assignedTag->work_center_name;
                    $row->_assigned_type = $assignedTag->opc_tag_type_name;
                    break;
                }
            }

            unset($row);
        }
        return $datatable;
    }
}
