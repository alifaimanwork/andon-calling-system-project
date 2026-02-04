<?php

namespace App\Http\Controllers\Web\Settings;

use App\Models\Part;
use App\Models\User;
use App\Models\Plant;
use App\Models\Reject;
use App\Models\RejectType;
use App\Models\WorkCenter;
use App\Models\RejectGroup;
use Illuminate\Http\Request;
use App\Extras\Datasets\PartDataset;
use App\Extras\Utils\ToastHelper;
use App\Http\Controllers\Controller;

class PartController extends Controller
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
        return view('pages.web.plant-settings.part.index', $viewData);
    }

    public function create(Request $request, $plant_uid)
    {
        //Show create new part page
        /** @var \App\Models\Plant $plant */
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();
        
        //Plant list group by regions
        $company = $plant->company;
        $regionPlants = [];
        foreach ($company->plants as $p) {
            $regionName = $p->region->name ?? '-';
            if (!isset($regionPlants[$regionName]))
                $regionPlants[$regionName] = [];

            $regionPlants[$regionName][] = $p;
        }
        
        $rejectTypes= $plant->onPlantDb()->rejectTypes()->get();
        //$line_Array dummy data (id, name, work_center_id)
        $viewData = [
            'topBarTitle' => 'PLANT SETTINGS',
            'plant' => $plant,
            'regionPlants' => $regionPlants,
            'currentUser' => User::getCurrent(),
            'workCenters' => $plant->onPlantDb()->workCenters()->get(),
            'sides' => $plant->onPlantDb()->parts()->pluck('side')->unique()->sort()->values(),
            'reject_types' => $rejectTypes,
            'reject_groups' => RejectGroup::on($plantConnection)->get(),
        ];

        // dd($viewData);
        return view('pages.web.plant-settings.part.create', $viewData);
    }

    //store
    public function store(Request $request, $plant_uid)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();
        //TODO: Check user permission for selected plant


        
        //TODO: Validate data
        $validatedData = $request->validate([
            //validate date format i:s or in s 

            'setup_time' => 'required|integer|min:0',
            'cycle_time' => 'required|integer|min:0',
            'opc_part_id' => 'required|integer',
            
        ]);

        //convert format MM:SS to seconds
        $setup_seconds = $request->setup_time;
        $cycle_seconds = $request->cycle_time;

        $part = new Part();
        $part->plant_id = $plant->id;
        $part->name = $request->part_name;
        $part->work_center_id = $request->work_center;
        $part->line_no = $request->line_number;
        $part->setup_time = $setup_seconds;
        $part->cycle_time = $cycle_seconds;
        $part->packaging = $request->packaging;
        $part->reject_target = $request->reject_target / 100;
        $part->side = $request->side;
        $part->enabled = 1;
        $part->opc_part_id = $request->opc_part_id;
        $part->part_no = $request->part_no;

        $part->setConnection($plantConnection)->save();

        $rejectTypes = $request->reject_types ?? [];

        $lockedRejectTypes = $plant->onPlantDb()->rejectTypes()->where('locked', '=', 1)->get();

        //if locked reject type is not in reject types, add it
        foreach ($lockedRejectTypes as $lockedRejectType) {
            if (!in_array($lockedRejectType->id, $rejectTypes)) {
                $rejectTypes[] = $lockedRejectType->id;
            }
        }

        //sync part reject types
        $part->partRejectTypes()->sync($rejectTypes);

        return redirect()->route('settings.part.index', $plant_uid);
    }

    //Edit 
    public function edit(Request $request, $plant_uid, $part_id)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();
        
        $part = Part::on($plantConnection)->findOrFail($part_id);

        $viewData = [
            'topBarTitle' => 'PLANT SETTINGS',
            'plant' => $plant,
            'part' => $part,
            'workCenters' => $plant->onPlantDb()->workCenters()->get(),
            'sides' => Part::on($plantConnection)->get()->pluck('side')->unique()->sort()->values(),
            'reject_types' => $plant->onPlantDb()->rejectTypes()->get(),
            'reject_groups' => RejectGroup::on($plantConnection)->get(),
            'part_reject_types' => $part->partRejectTypes()->where('part_id', '=', $part_id)->get(),
        ];

        //dd($viewData);
        return view('pages.web.plant-settings.part.edit', $viewData);

    }

    //update
    public function update(Request $request, $plant_uid, $part_id)
    {


        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        $part = Part::on($plantConnection)->findOrFail($part_id);

        //TODO: Input Validation

        $part->work_center_id = $request->work_center;
        $part->line_no = $request->line_number;
        $part->part_no = $request->part_no;
        $part->name = $request->part_name;
        $part->packaging = $request->packaging;
        $part->reject_target = $request->reject_target / 100;
        $part->side = $request->side;
        $part->opc_part_id = $request->opc_part_id;
        //TODO: Add enable/disable function
        $part->setup_time = minSecToTotalSeconds($request->setup_time);
        $part->cycle_time = minSecToTotalSeconds($request->cycle_time);

        $part->setConnection($plantConnection)->save();

        $rejectTypes = $request->reject_types ?? [];

        $lockedRejectTypes = $plant->onPlantDb()->rejectTypes()->where('locked', '=', 1)->get();

        //if locked reject type is not in reject types, add it
        foreach ($lockedRejectTypes as $lockedRejectType) {
            if (!in_array($lockedRejectType->id, $rejectTypes)) {
                $rejectTypes[] = $lockedRejectType->id;
            }
        }
        
        $part->partRejectTypes()->sync($rejectTypes);

        return redirect()->route('settings.part.index', $plant_uid);
    }

    //Delete
    public function destroy(Request $request, $plant_uid, $part_id)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        /** @var \App\Models\Part $part */
        $part = Part::on($plantConnection)->findOrFail($part_id);
        //TODO: DELETE PLANT DATABASE VERIFICATION
        if (!$part->isDestroyable()) {
            ToastHelper::addToast('Unable to delete ' . $part->name . '.', 'Delete Part', 'danger');
            return redirect()->route('settings.part.index', $plant_uid);
        }

        $part->setConnection($plantConnection)->delete();

        return redirect()->route('settings.part.index', $plant_uid);
    }

    //DATATABLE
    public function datatable(Request $request, $plant_uid)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();

        //$dataset = new PartDataset();
        $dataset = new PartDataset();

        return $dataset->setPlant($plant)->datatable($request);
    }
    
    
}

