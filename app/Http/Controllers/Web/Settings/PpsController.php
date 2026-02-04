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
use App\Extras\Datasets\ProductionOrderDataset;
use App\Extras\Datasets\ProductionPlanningSheetDataset;
use App\Extras\Payloads\GenericRequestResult;
use App\Extras\Traits\TerminalRoutingTrait;
use App\Extras\Traits\WorkCenterTrait;
use App\Models\ProductionOrder;
use App\Models\ShiftType;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PpsController extends Controller
{

    public function index(Request $request, $plant_uid)
    {
        // Find the plant based on UID
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        // TODO: Check user permission for selected plant
    
        // Fetch work centers associated with the current plant directly from the database
        $workcenters = DB::table('ipos_plant.work_centers')
                        ->where('plant_id', $plant->id) // Assuming 'plant_id' is the foreign key column
                        ->get();
    
        // Fetch parts related to the current plant and filtered by work center
        $parts = DB::table('ipos_plant.parts')
                    ->where('plant_id', $plant->id) // Filter parts by plant (optional, if needed)
                    //->where('work_center_id', '<>', null) // Filter parts where work_center_id is not null
                    ->get();
    
        $viewData = [
            'topBarTitle' => 'PLANT SETTINGS',
            'plant' => $plant,
            'workcenters' => $workcenters, // Pass fetched data to the view
            'parts' => $parts, // Pass fetched data to the view
        ];
    
        return view('pages.web.plant-settings.pps.index', $viewData);
    }

        public function showDataInputForm()
    {
        return view('pages.web.plant-settings.pps.index');
    }

    public function downloadCSV(Request $request, $plant_uid)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();
        //TODO: Check user permission for selected plant
        
        $data = json_decode($request->input('data'), true);
    
        if (empty($data)) {
            return redirect()->back()->with('error', 'No data to download.');
        }
    
        $csvContent = implode("\n", array_map(function ($row) {
            return implode(';', $row);
        }, $data));
    
        // Generate a unique file name using a timestamp
        $fileName = 'uploads/PPS_' . time() . '.csv';
    
        // Store the CSV file in the storage/app/uploads directory
        Storage::put($fileName, $csvContent);
    
        // Create a public symbolic link to make the file accessible from the web
        $publicPath = 'uploads/' . $fileName;
        Storage::delete($publicPath); // Remove the existing link to avoid conflicts
    
        // Display success message as an alert
        $successMessage = 'CSV file generated successfully.';
        echo "<script>alert('$successMessage');</script>";

        $viewData = [
            'topBarTitle' => 'PLANT SETTINGS',
        ];
    
        // Redirect to the 'index' function
        return redirect()->route('settings.pps.csv', $plant_uid);
    }
}
