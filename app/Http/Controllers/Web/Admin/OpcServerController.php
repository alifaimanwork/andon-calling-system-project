<?php

namespace App\Http\Controllers\Web\Admin;

use App\Extras\Datasets\OpcActiveTagDataset;
use App\Extras\Datasets\OpcLogDataset;
use App\Extras\Datasets\OpcServerDataset;
use App\Extras\Utils\ToastHelper;
use App\Http\Controllers\Controller;
use App\Jobs\SignalOpcSettingUpdated;
use App\Models\OpcActiveTag;
use App\Models\OpcServer;
use App\Models\OpcTag;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OpcServerController extends Controller
{
    public function test(Request $request)
    {
        $activeTags = OpcActiveTag::get();

        if ($request->wantsJson())
            return ['data' => $activeTags];

        return view('pages.web.opc-taglist');
    }

    public function logs(Request $request)
    {
        $activeTags = OpcActiveTag::get();
        return view('pages.web.opc-logs', ['active_tags' => $activeTags]);
    }
    public function logDataset(Request $request)
    {
        $dataset = new OpcLogDataset();

        return $dataset->datatable($request);
    }

    public function syncTags(Request $request, OpcServer $opcServer)
    {
        return $opcServer->syncTags();
    }
    public function assignTags(Request $request, OpcServer $opcServer)
    {
        $request->validate(['tags' => 'required', 'array', 'plant_id' => 'required', 'integer']);

        if (!$opcServer)
            abort(404);


        //get activetags
        $opcTags = OpcActiveTag::whereIn('id', $request->tags)->where('opc_server_id', $opcServer->id)->get();
        if ($request->plant_id <= 0) {
            //unassignment

            /** @var \App\Models\OpcActiveTag $tag */
            foreach ($opcTags as $tag) {
                if (!is_null($tag->plant_id)) {
                    $tag->plant_id = null;
                }
                $tag->save();
            }
        } else {
            /** @var \App\Models\Plant $plant */
            $plant = Plant::find($request->plant_id);
            if (!$plant)
                return ['result' => -1, 'message' => 'invalid plant'];

            /** @var \App\Models\OpcActiveTag $tag */
            foreach ($opcTags as $tag) {
                $tag->plant_id = $plant->id;
                $tag->save();

            }

            return ['result' => 0, 'message' => 'ok'];
        }
    }
    public function forceResyncOpcServer(Request $request, OpcServer $opcServer)
    {
        return $opcServer->resyncOpcServer();
    }
    //TODO: Resource Guard
    public function index(Request $request)
    {
        $viewData = [
            'topBarTitle' => 'I-POS SETTINGS'
        ];
        return view('pages.web.admin.opc-server.index', $viewData);
    }
    public function create(Request $request)
    {
        //Show create new Opc Server page
        $viewData = [
            'topBarTitle' => 'I-POS SETTINGS'
        ];

        return view('pages.web.admin.opc-server.create', $viewData);
    }

    public function store(Request $request)
    {
        //Store new opc server
        //TODO: Input Validation

        $validationRules = [
            'name' => ['required', 'string', 'max:64'],
            'hostname' => ['required', 'string', 'max:255'],
            'port' => ['required', 'integer', 'min:1', 'max:65535'],
            'adapter_hostname' => ['required', 'string', 'max:255'],
            'adapter_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'configuration_data' => ['nullable', 'string', 'max:65535']
        ];

        $request->validate($validationRules);
        $data = $request->only(array_keys($validationRules));

        $newServer = new OpcServer($data);

        //TODO: test opc connection, fetch all tags
        $newServer->save();

        $viewData = [
            'topBarTitle' => 'I-POS SETTINGS'
        ];

        ToastHelper::addToast('New OPC server added.', 'Create New OPC Server');
        return view('pages.web.admin.opc-server.index', $viewData);
    }
    public function edit(Request $request, OpcServer $opcServer)
    {
        $plants = Plant::get();
        //Edit opc-server details
        $viewData = [
            'topBarTitle' => 'I-POS SETTINGS',
            'opcServer' => $opcServer,
            'plants' => $plants
        ];
        return view('pages.web.admin.opc-server.edit', $viewData);
    }
    public function show(Request $request, OpcServer $opcServer)
    {
        //Show opc-server details
        if ($request->wantsJson())
            return $opcServer;

        abort(404); //No view page
    }

    public function update(Request $request, OpcServer $opcServer)
    {
        //Update opc-server

        //TODO: Input Validation

        //temporary validation
        $timeZones = \DateTimeZone::listIdentifiers();
        $validationRules = [
            'name' => ['required', 'string', 'max:64'],
            'hostname' => ['required', 'string', 'max:255'],
            'port' => ['required', 'integer', 'min:1', 'max:65535'],
            'adapter_hostname' => ['required', 'string', 'max:255'],
            'adapter_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'configuration_data' => ['nullable', 'string', 'max:65535']
        ];

        $request->validate($validationRules);

        $data = $request->only(array_keys($validationRules));

        $opcServer->update($data);
        ToastHelper::addToast($opcServer->name . ' updated.', 'Update OPC Server');
        return redirect()->route('admin.opc-server.index');
    }
    public function destroy(Request $request, OpcServer $opcServer)
    {
        //Delete opc-server
        //TODO: Guard against accidental delete
        if (!$opcServer->isDestroyable()) {
            ToastHelper::addToast('Unable to delete ' . $opcServer->name . '.', 'Delete OPC Server', 'danger');
            return redirect()->route('admin.opc-server.index');
        }

        $opcServer->forceDelete();
        ToastHelper::addToast($opcServer->name . ' deleted.', 'Delete OPC Server', 'danger');

        return redirect()->route('admin.opc-server.index');
    }
    public function datatable(Request $request)
    {
        $dataset = new OpcServerDataset();
        return $dataset->datatable($request);
    }

    public function activeTagDatatable(Request $request)
    {
        $dataset = new OpcActiveTagDataset();

        if ($request->search) {
            foreach ($request->search as $parameter) {
                if ($parameter['field'] == '_assigned') {
                    if ($parameter['parameter'] == 'assigned')
                        $dataset->setFilters('assigned', 1);
                    elseif ($parameter['parameter'] == 'unassigned')
                        $dataset->setFilters('assigned', 0);
                }
            }
        }

        return $dataset->datatable($request);
    }
}
