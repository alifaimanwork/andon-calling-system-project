<?php

namespace App\Http\Controllers\Web\Settings;

use App\Models\Plant;
use App\Models\Downtime;
use Illuminate\Http\Request;
use App\Models\DowntimeReason;
use App\Http\Controllers\Controller;
use App\Extras\Datasets\DowntimeReasonDataset;

class DowntimeReasonController extends Controller
{
    //TODO: Resource Guard
    // public function index(Request $request, $plant_uid, $downtime_id)
    // {
    //     $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
    //     //TODO: Check user permission for selected plant

    //     $viewData = [
    //         'topBarTitle' => 'PLANT SETTINGS',
    //         'plant' => $plant
    //     ];
    //     return view('pages.web.plant-settings.downtime-reason.index', $viewData);
    // }

    public function create(Request $request, $plant_uid, $downtime_id)
    {
        //Show create new part page
        /** @var \App\Models\Plant $plant */
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        //TODO: Check user permission for selected plant


        //Plant list group by regions
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        $reasons = DowntimeReason::on($plantConnection)->get();
        //remove duplicate
        $reasons = $reasons->unique('reason');

        $downtime = Downtime::on($plantConnection)->where('id', $downtime_id)->firstOrFail();

        $viewData = [
            'topBarTitle' => 'PLANT SETTINGS',
            'plant' => $plant,
            'reasons' => $reasons,
            'downtime' => $downtime
        ];

        return view('pages.web.plant-settings.downtime-reason.create', $viewData);
    }

    // store
    public function store(Request $request, $plant_uid, $downtime_id)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        $downtime = Downtime::on($plantConnection)->where('id', $downtime_id)->firstOrFail();

        $downtimeReason = new DowntimeReason();
        $downtimeReason->downtime_id = $downtime->id;
        $downtimeReason->reason = $request->reason;
        $downtimeReason->enable_user_input = $request->user_input;
        $downtimeReason->enabled = $request->enabled;
        $downtimeReason->setConnection($plantConnection)->save();

        return redirect()->route('settings.downtime.edit', [$plant_uid, $downtime_id]);
    }

    //edit
    public function edit(Request $request, $plant_uid, $downtime_id, $downtime_reason_id)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        $downtime = Downtime::on($plantConnection)->where('id', $downtime_id)->firstOrFail();
        $downtimeReason = DowntimeReason::on($plantConnection)->where('id', $downtime_reason_id)->firstOrFail();

        // dd($plant_uid, $downtime_id, $downtime_reason_id, $downtime, $downtimeReason);
        $viewData = [
            'topBarTitle' => 'PLANT SETTINGS',
            'plant' => $plant,
            'downtime' => $downtime,
            'downtimeReason' => $downtimeReason,
            'reasons' => DowntimeReason::on($plantConnection)->get()->unique('reason'), //list
        ];

        return view('pages.web.plant-settings.downtime-reason.edit', $viewData);
    }

    // update
    public function update(Request $request, $plant_uid, $downtime_id, $downtime_reason_id)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        $downtime = Downtime::on($plantConnection)->where('id', $downtime_id)->firstOrFail();
        $downtimeReason = DowntimeReason::on($plantConnection)->where('id', $downtime_reason_id)->firstOrFail();

        $downtimeReason->reason = $request->reason;
        $downtimeReason->enable_user_input = $request->user_input;
        $downtimeReason->enabled = $request->enabled;
        $downtimeReason->setConnection($plantConnection)->save();

        return redirect()->route('settings.downtime.edit', [$plant_uid, $downtime_id]);
    }

    // destroy
    public function destroy(Request $request, $plant_uid, $downtime_id, $downtime_reason_id)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        $downtime = Downtime::on($plantConnection)->where('id', $downtime_id)->firstOrFail();
        $downtimeReason = DowntimeReason::on($plantConnection)->where('id', $downtime_reason_id)->firstOrFail();

        //TODO: DELETE PLANT DATABASE VERIFICATION
        dd("TODO: Verify delete", $request->all(), $downtime, $downtimeReason);
        $downtimeReason->setConnection($plantConnection)->delete();

        return redirect()->route('settings.downtime.edit', [$plant_uid, $downtime_id]);
    }
    

    public function datatable(Request $request, $plant_uid)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $downtime_id = $request->id;

        $dataset_reason = new DowntimeReasonDataset();
        return $dataset_reason->setPlant($plant)->setFilters('downtime_id', $downtime_id)->datatable($request);
    }
}
