<?php

namespace App\Http\Controllers\Web\Settings;

use App\Models\Plant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Extras\Datasets\FactoryDataset;
use App\Extras\Utils\ToastHelper;
use App\Models\Factory;

class FactoryController extends Controller
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
        return view('pages.web.plant-settings.factory.index', $viewData);
    }

    public function create(Request $request, $plant_uid)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();

        $viewData = [
            'topBarTitle' => 'PLANT SETTINGS',
            'plant' => $plant
        ];

        return view('pages.web.plant-settings.factory.create', $viewData);
    }

    public function store(Request $request, $plant_uid)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        //check if already exists in database
        $factory_uid = Factory::on($plantConnection)->where('uid', '=', $request->input('uid'))->first();

        if ($factory_uid) {
            $error['uid'] = 'Factory with this UID already exists';
            return redirect()->back()->withErrors($error)->withInput();
        }

        $factory = new Factory();
        $factory->plant_id = $plant->id;
        $factory->uid = $request->input('uid');
        $factory->name = $request->input('name');
        $factory->setConnection($plantConnection)->save();

        return redirect()->route('settings.factory.index', [$plant_uid]);
    }

    //edit
    public function edit(Request $request, $plant_uid, $factory_id)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();
        $factory = Factory::on($plantConnection)->findOrFail($factory_id);

        $viewData = [
            'topBarTitle' => 'PLANT SETTINGS',
            'plant' => $plant,
            'factory' => $factory
        ];

        return view('pages.web.plant-settings.factory.edit', $viewData);
    }

    //update
    public function update(Request $request, $plant_uid, $factory_id)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();
        $factory = Factory::on($plantConnection)->findOrFail($factory_id);

        //TODO: Validate input
        $factory_uid = Factory::on($plantConnection)->where('uid', '=', $request->input('uid'))->first();

        if ($factory_uid) {
            $error['uid'] = 'Factory with this UID already exists';
            return redirect()->back()->withErrors($error)->withInput();
        }


        $factory->uid = $request->input('uid');
        $factory->name = $request->input('name');
        $factory->save();

        return redirect()->route('settings.factory.index', [$plant_uid]);
    }

    //delete
    public function destroy(Request $request, $plant_uid, $factory_id)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        /** @var \App\Models\Factory $factory */
        $factory = Factory::on($plantConnection)->findOrFail($factory_id);

        if (!$factory->isDestroyable()) {
            ToastHelper::addToast('Unable to delete ' . $factory->name . '.', 'Delete Factory', 'danger');
            return redirect()->route('settings.factory.index', [$plant_uid]);
        }

        $factory->delete();

        return redirect()->route('settings.factory.index', [$plant_uid]);
    }

    //datatable
    public function datatable(Request $request, $plant_uid)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();

        $dataset = new FactoryDataset();

        return $dataset->setPlant($plant)->datatable($request);
    }
}
