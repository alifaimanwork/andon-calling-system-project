<?php

namespace App\Http\Controllers\Web\Settings;

use App\Models\Plant;
use App\Models\Downtime;
use App\Models\DowntimeType;
use Illuminate\Http\Request;
use App\Models\DowntimeReason;

use App\Http\Controllers\Controller;
use App\Extras\Datasets\DowntimeDataset;
use App\Extras\Datasets\DowntimeReasonDataset;
use App\Extras\Utils\ToastHelper;

class DowntimeController extends Controller
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
        return view('pages.web.plant-settings.downtime.index', $viewData);
    }

    public function create(Request $request, $plant_uid)
    {
        //Show create new part page
        /** @var \App\Models\Plant $plant */
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        //TODO: Check user permission for selected plant



        //Plant list group by regions
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        $type = DowntimeType::on($plantConnection)->get();

        $viewData = [
            'topBarTitle' => 'PLANT SETTINGS',
            'plant' => $plant,
            'types' => $type
        ];

        return view('pages.web.plant-settings.downtime.create', $viewData);
    }

    public function edit(Request $request, $plant_uid, $downtime_id)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        $downtime = $plant->onPlantDb()->downtimes()->where('id', $downtime_id)->firstOrFail();

        $viewData = [
            'topBarTitle' => 'PLANT SETTINGS',
            'plant' => $plant,
            'downtime' => $downtime,
            'types' => DowntimeType::on($plantConnection)->get(),

        ];

        // dd($viewData);
        return view('pages.web.plant-settings.downtime.edit', $viewData);
    }


    public function store(Request $request, $plant_uid)
    {
        /** @var \App\Models\Plant $plant */
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();

        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();
        //TODO: Check user permission for selected plant

        //TODO: Validate data
        $request->validate([
            'type' => ['required', 'integer', 'exists:' . DowntimeType::TABLE_NAME . ',id'],
            'category' => ['required', 'string']
        ]);

        $downtime = new Downtime();
        $downtime->plant_id = $plant->id;
        $downtime->downtime_type_id = $request->type;
        $downtime->category = $request->category;
        $downtime->enabled = 1;
        $downtime->setConnection($plantConnection)->save();


        return redirect()->route('settings.downtime.index', $plant_uid);
    }

    public function update(Request $request, $plant_uid, $downtime_id)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        //TODO: Validate data
        $request->validate([
            'type' => ['required', 'integer', 'exists:' . DowntimeType::TABLE_NAME . ',id'],
            'category' => ['required', 'string']
        ]);

        $downtime = Downtime::on($plantConnection)->where('id', $downtime_id)->firstOrFail();

        $downtime->downtime_type_id = $request->type;
        $downtime->category = $request->category;
        $downtime->enabled = $request->enabled;
        $downtime->setConnection($plantConnection)->save();

        return redirect()->route('settings.downtime.index', $plant_uid);
    }

    // show 
    public function show(Request $request, $plant_uid, $downtime_id)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        $downtime = Downtime::on($plantConnection)->where('id', $downtime_id)->firstOrFail();

        dd("show");
    }

    // destroy
    public function destroy(Request $request, $plant_uid, $downtime_id)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        /** @var \App\Models\Downtime $downtime */
        $downtime = Downtime::on($plantConnection)->where('id', $downtime_id)->firstOrFail();

        //TODO: DELETE PLANT DATABASE VERIFICATION
        if (!$downtime->isDestroyable()) {
            ToastHelper::addToast('Unable to delete ' . $downtime->category . '.', 'Delete Downtime', 'danger');
            return redirect()->route('settings.downtime.index', $plant_uid);
        }

        $downtime->setConnection($plantConnection)->delete();

        return redirect()->route('settings.downtime.index', $plant_uid);
    }



    public function datatable(Request $request, $plant_uid)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();

        $dataset = new DowntimeDataset();

        return $dataset->setPlant($plant)->datatable($request);
    }
}
