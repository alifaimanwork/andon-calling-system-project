<?php

namespace App\Http\Controllers\Web\Settings;

use App\Extras\Datasets\FactoryDataset;
use App\Extras\Datasets\MonitorClientDataset;
use App\Extras\Rules\ValidHost;
use App\Extras\Utils\ToastHelper;
use App\Http\Controllers\Controller;
use App\Models\MonitorClient;
use App\Models\Plant;
use App\Models\WorkCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NetworkStatusController extends Controller
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
        return view('pages.web.plant-settings.network-status.index', $viewData);
    }

    public function create(Request $request, $plant_uid)
    {
        /** @var \App\Models\Plant $plant */
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $workCenters = $plant->onPlantDb()->workCenters()->get();

        $viewData = [
            'topBarTitle' => 'PLANT SETTINGS',
            'plant' => $plant,
            'workCenters' => $workCenters
        ];

        return view('pages.web.plant-settings.network-status.create', $viewData);
    }

    public function store(Request $request, $plant_uid)
    {
        /** @var \App\Models\Plant $plant */
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        $request->validate([
            'client_type' => 'required|integer|min:0|max:2',
            'name' => 'required'
        ]);

        $client = new MonitorClient();
        $client->plant_id = $plant->id;
        $client->client_type = intval($request->client_type);

        if ($request->client_type == 2) {
            //check hostname
            $request->validate([
                'target_host' => ['required', new ValidHost()]
            ]);

            $client->client_info = json_encode(['host' => $request->target_host]);
        } elseif ($request->client_type == 0 || $request->client_type == 1) {
            $request->validate([
                'target_id' => 'exists:' . $plant->getPlantConnection() . '.' . WorkCenter::TABLE_NAME . ',id'
            ]);
            $client->target_id = $request->target_id;
        } else {
            abort(400);
        }
        $client->name = $request->name;
        $client->state = MonitorClient::STATE_UNKNOWN;
        $client->uid = MonitorClient::generateNewUid($plant);

        $client->probe()->setConnection($plantConnection)->save();

        return redirect()->route('settings.network-status.index', [$plant_uid]);
    }

    //edit
    public function edit(Request $request, $plant_uid, $client_id)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();
        $client = MonitorClient::on($plantConnection)->findOrFail($client_id);
        $client->client_info = json_decode($client->client_info);
        $workCenters = $plant->onPlantDb()->workCenters()->get();

        $viewData = [
            'topBarTitle' => 'PLANT SETTINGS',
            'plant' => $plant,
            'monitorClient' => $client,
            'workCenters' => $workCenters
        ];

        return view('pages.web.plant-settings.network-status.edit', $viewData);
    }

    //update
    public function update(Request $request, $plant_uid, $client_id)
    {
        /** @var \App\Models\Plant $plant */
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        $request->validate([
            'client_type' => 'required|integer|min:0|max:2',
            'name' => 'required'
        ]);

        $client = $plant->onPlantDb()->monitorClients()->where('id', $client_id)->firstOrFail();

        $client->plant_id = $plant->id;
        $client->client_type = intval($request->client_type);

        if ($request->client_type == 2) {
            //check hostname
            $request->validate([
                'target_host' => ['required', new ValidHost()]
            ]);

            $client->client_info = json_encode(['host' => $request->target_host]);
        } elseif ($request->client_type == 0 || $request->client_type == 1) {
            $request->validate([
                'target_id' => 'exists:' . $plant->getPlantConnection() . '.' . WorkCenter::TABLE_NAME . ',id'
            ]);
            $client->target_id = $request->target_id;
        } else {
            abort(400);
        }
        $client->name = $request->name;
        $client->state = MonitorClient::STATE_UNKNOWN;
        $client->probe()->setConnection($plantConnection)->save();

        return redirect()->route('settings.network-status.index', [$plant_uid]);
    }

    //delete
    public function destroy(Request $request, $plant_uid, $client_id)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        /** @var \App\Models\Factory $client */
        $client = MonitorClient::on($plantConnection)->findOrFail($client_id);

        if (!$client->isDestroyable()) {
            ToastHelper::addToast('Unable to delete ' . $client->name . '.', 'Delete Factory', 'danger');
            return redirect()->route('settings.network-status.index', [$plant_uid]);
        }

        $client->delete();

        return redirect()->route('settings.network-status.index', [$plant_uid]);
    }

    //datatable
    public function datatable(Request $request, $plant_uid)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();

        $dataset = new MonitorClientDataset();

        return $dataset->setPlant($plant)->datatable($request);
    }
}
