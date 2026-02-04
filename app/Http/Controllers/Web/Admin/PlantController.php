<?php

namespace App\Http\Controllers\Web\Admin;

use App\Extras\Datasets\PlantDataset;
use App\Extras\Utils\SvgLayoutHelper;
use App\Extras\Utils\ToastHelper;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\OpcServer;
use App\Models\Plant;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;

class PlantController extends Controller
{
    //TODO: Resource Guard
    public function index(Request $request)
    {
        $viewData = [
            'topBarTitle' => 'I-POS SETTINGS'
        ];
        return view('pages.web.admin.plant.index', $viewData);
    }
    public function create(Request $request)
    {
        //Show create new plant page
        $companies = Company::get(); //TODO: show only active company
        $regions = Region::get();
        $viewData = [
            'topBarTitle' => 'I-POS SETTINGS',
            'companies' => $companies,
            'regions' => $regions,
        ];

        return view('pages.web.admin.plant.create', $viewData);
    }

    private function loadDefaultDBConfig() //Temporary
    {
        return [
            "host" => env('TESTPLANT_DB_HOST', '127.0.0.1'),
            "port" => env('TESTPLANT_DB_PORT', '3306'),
            "database" => env('TESTPLANT_DB_DATABASE', 'ipos'),
            "username" => env('TESTPLANT_DB_USERNAME', 'root'),
            "password" => env('TESTPLANT_DB_PASSWORD', ''),
        ];
    }

    public function store(Request $request)
    {
        //Store new plant
        //TODO: Input Validation

        //temporary validation
        $timeZones = \DateTimeZone::listIdentifiers();
        $validationRules = [
            'name' => ['required', 'string', 'max:64', 'unique:plants,name'],
            'uid' => ['required', 'string', 'max:16', 'unique:plants,uid'],
            'sap_id' => ['required', 'string', 'max:64'],
            'time_zone' => ['required', Rule::in($timeZones)],
            'total_employee' => ['nullable', 'integer'],
            'total_production_line' => ['nullable', 'integer'],
            //'overview_layout_data' => ['required', 'string', 'max:1000000'],
            'overview_layout_file' => ['nullable', 'image', 'mimes:svg', 'max:1024'],
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'region_id' => ['required', 'integer', 'exists:regions,id'],
        ];

        $request->validate($validationRules);
        $data = $request->only(array_keys($validationRules));

        unset($data['overview_layout_file']);
        $layoutFile = $request->file('overview_layout_file', null);
        if ($layoutFile)
            $data['overview_layout_data'] = SvgLayoutHelper::removeXmlTag($layoutFile->get());

        $newPlant = new Plant($data);

        //TODO: have user input db config
        //TODO: test connection for user input db
        $newPlant->database_configurations = json_encode($this->loadDefaultDBConfig()); //temp config use from env.

        $newPlant->save();

        $opcServers = OpcServer::get(); //default all opc server usable by plant
        foreach ($opcServers as $opcServer) {
            $newPlant->opcServers()->attach($opcServer);
        }




        $viewData = [
            'topBarTitle' => 'I-POS SETTINGS'
        ];

        ToastHelper::addToast('New ' . $newPlant->name . ' added.', 'Create New Plant');
        return view('pages.web.admin.plant.index', $viewData);
    }
    public function edit(Request $request, Plant $plant)
    {
        //Edit plant details
        $companies = Company::get(); //TODO: show only active company
        $regions = Region::get();

        $viewData = [
            'topBarTitle' => 'I-POS SETTINGS',
            'plant' => $plant,
            'companies' => $companies,
            'regions' => $regions,
        ];
        return view('pages.web.admin.plant.edit', $viewData);
    }
    public function show(Request $request, Plant $plant)
    {
        //Show plant details
        if ($request->wantsJson())
            return $plant;

        abort(404); //No view page
    }

    public function update(Request $request, Plant $plant)
    {
        //Update plant

        //TODO: Input Validation

        //temporary validation
        $timeZones = \DateTimeZone::listIdentifiers();
        $validationRules = [
            'name' => ['required', 'string', 'max:64', 'unique:plants,name,' . $plant->id],
            'uid' => ['required', 'string', 'max:16', 'unique:plants,uid,' . $plant->id],
            'sap_id' => ['required', 'string', 'max:64'],
            'time_zone' => ['required', Rule::in($timeZones)],
            'total_employee' => ['nullable', 'integer'],
            'total_production_line' => ['nullable', 'integer'],
            //'overview_layout_data' => ['required', 'string', 'max:1000000'],
            'overview_layout_file' => ['nullable', 'image', 'mimes:svg', 'max:1024'],

            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'region_id' => ['required', 'integer', 'exists:regions,id'],
        ];

        $request->validate($validationRules);

        $data = $request->only(array_keys($validationRules));

        unset($data['overview_layout_file']);
        $layoutFile = $request->file('overview_layout_file', null);
        if ($layoutFile)
            $data['overview_layout_data'] = SvgLayoutHelper::removeXmlTag($layoutFile->get());

        $plant->update($data);
        ToastHelper::addToast($plant->name . ' updated.', 'Update Plant');
        return redirect()->route('admin.plant.index');
    }
    public function destroy(Request $request, Plant $plant)
    {
        //Delete plant
        //TODO: Guard against accidental delete
        if (!$plant->isDestroyable()) {
            ToastHelper::addToast('Unable to delete ' . $plant->name . '.', 'Delete Plant', 'danger');
            return redirect()->route('admin.plant.index');
        }

        $plant->forceDelete();
        ToastHelper::addToast($plant->name . ' deleted.', 'Delete Plant', 'danger');

        return redirect()->route('admin.plant.index');
    }
    public function datatable(Request $request)
    {
        $dataset = new PlantDataset();
        return $dataset->datatable($request);
    }
}
