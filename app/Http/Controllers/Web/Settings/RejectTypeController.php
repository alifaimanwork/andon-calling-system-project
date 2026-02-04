<?php

namespace App\Http\Controllers\Web\Settings;

use App\Models\Plant;
use App\Models\RejectType;
use App\Models\RejectGroup;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Extras\Datasets\RejectTypeDataset;
use App\Extras\Utils\ToastHelper;

class RejectTypeController extends Controller
{
    //TODO: Resource Guard
    public function index(Request $request, $plant_uid)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();
        //TODO: Check user permission for selected plant

        $viewData = [
            'topBarTitle' => 'PLANT SETTINGS',
            'plant' => $plant,
            'groups' => RejectGroup::on($plantConnection)->get(),
        ];

        return view('pages.web.plant-settings.reject-type.index', $viewData);
    }

    //create new reject type
    public function create(Request $request, $plant_uid, $group_id)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        $group = RejectGroup::on($plantConnection)->findOrFail($group_id);

        $viewData = [
            'topBarTitle' => 'PLANT SETTINGS',
            'plant' => $plant,
            'group' => $group,
            'all_groups' => RejectGroup::on($plantConnection)->get(),
        ];

        return view('pages.web.plant-settings.reject-type.create', $viewData);

    }

    //store 
    public function store(Request $request, $plant_uid)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        $rejectType = new RejectType();
        $rejectType->name = $request->input('type');
        $rejectType->reject_group_id = $request->input('group_id');
        $rejectType->plant_id = $plant->id;
        $rejectType->enabled = $request->input('enabled');
        $rejectType->setConnection($plantConnection)->save();

        return redirect()->route('settings.reject-type.index', ['plant_uid' => $plant_uid]);
    }

    //edit
    public function edit(Request $request, $plant_uid, $reject_id)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        $rejectType = RejectType::on($plantConnection)->findOrFail($reject_id);

        $isLocked = $rejectType->locked; 
        
        if ($isLocked) {
            abort(404);
        }

        $viewData = [
            'topBarTitle' => 'PLANT SETTINGS',
            'plant' => $plant,
            'rejectType' => $rejectType,

            //unique reject types
            'all_reject_types' => RejectType::on($plantConnection)->get()->unique('name'),
        ];

        return view('pages.web.plant-settings.reject-type.edit', $viewData);
    }

    //store
    public function update(Request $request, $plant_uid, $reject_id)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        $rejectType = RejectType::on($plantConnection)->findOrFail($reject_id);
        $rejectType->name = $request->input('type');
        $rejectType->reject_group_id = $request->input('group_id');
        $rejectType->enabled = $request->input('enabled');
        $rejectType->setConnection($plantConnection)->save();

        return redirect()->route('settings.reject-type.index', ['plant_uid' => $plant_uid]);
    }

    //delete
    public function destroy(Request $request, $plant_uid, $reject_id)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        /** @var \App\Models\RejectType $rejectType */
        $rejectType = RejectType::on($plantConnection)->findOrFail($reject_id);
        //TODO: DELETE PLANT DATABASE VERIFICATION

        if (!$rejectType->isDestroyable()) {
            ToastHelper::addToast('Unable to delete ' . $rejectType->name . '.', 'Delete Reject Type', 'danger');
            return redirect()->route('settings.reject-type.index', ['plant_uid' => $plant_uid]);
        }
        
        $rejectType->setConnection($plantConnection)->delete();

        return redirect()->route('settings.reject-type.index', ['plant_uid' => $plant_uid]);
    }

    //datatable
    public function datatable(Request $request, $plant_uid, $group_id)
    {
        $group_id = (int)$group_id;
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $dataset = new RejectTypeDataset();

        return $dataset->setPlant($plant)->setFilters('reject_group_id', $group_id)->datatable($request);
    }
}
