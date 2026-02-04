<?php

namespace App\Http\Controllers\Web\Settings;

use App\Http\Controllers\Controller;
use App\Models\Plant;
use App\Models\Shift;
use Illuminate\Http\Request;
use Nette\Utils\Validators;

class ShiftController extends Controller
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
            'shifts' => Shift::on($plantConnection)->where('plant_id', '=', $plant->id)->with('shiftType')->get(),
        ];

        return view('pages.web.plant-settings.shift.index', $viewData);
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
            'shifts' => Shift::on($plantConnection)->where('plant_id', '=', $plant->id)->with('shiftType')->get(),
        ];

        return view('pages.web.plant-settings.shift.create', $viewData);
    }

    //update
    public function update(Request $request, $plant_uid)
    {
        /** @var \App\Models\Plant $plant */
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        //request json to array 
        $requestJson = $request->get('shift_data');
        $requestArray = json_decode($requestJson, true);

        $isInvalid = false;
      
        //check valid total shift count
        if (count($requestArray) != 14) {
            $isInvalid = __LINE__;
        } else {
            $shiftData = [];

            for ($day = 1; $day <= 7; $day++) {
                $shiftData[$day] = [];
            }

            //Check valid shift type
            foreach ($requestArray as $shift) {
                //check day of week
                $dayOfWeek = $shift['day_of_week'];
                if ($dayOfWeek < 1 || $dayOfWeek > 7) {
                    $isInvalid = __LINE__;
                    break;
                }

                if ($shift['shift_type'] == 'day')
                    $shiftTypeId = 1;
                elseif ($shift['shift_type'] == 'night')
                    $shiftTypeId = 2;
                else {
                    $isInvalid = __LINE__;
                    break;
                }

                //check duplicate
                if (isset($shiftData[$day][$shiftTypeId])) {
                    $isInvalid = __LINE__;
                    break;
                }
                $shiftData[$day][$shiftTypeId] = $shift;
            }
        }

        if (!$isInvalid) {
            //handle error
            $error_message['error'] = 'Invalid shift data';
            return redirect()->back()->withErrors($error_message)->withInput();
        }

        $error_message = [];

        // dd($requestArray);

        foreach ($requestArray as &$shift) {
            //if $shift['shift_type'] == 'day' then $shift['shift_type_id'] = 1. else $shift['shift_type_id'] = 2
            if ($shift['shift_type'] == 'day') {
                $shift['shift_type_id'] = 1;
            } else {
                $shift['shift_type_id'] = 2;
            }

            $startTime = $shift['start_time'];
            $endTime = $shift['end_time'];

            $startTime = \DateTime::createFromFormat('H:i', $startTime);
            $endTime = \DateTime::createFromFormat('H:i', $endTime);

            //start_time must be in format HH:MM and not empty
            if (!$startTime) {
                $error_message['error'] = 'start_time must be in format HH:MM and not empty';
            }

            //end_time must be in format HH:MM and not empty
            if (!$endTime) {
                $error_message['error'] = 'end_time must be in format HH:MM and not empty';
            }
            unset($shift);
        }
        
        
        $overlapped = Shift::disableOverlapShifts($requestArray);

        if ($overlapped) {
            $error_message['error'] = 'Overlapped shift detected and shift has been disabled';
        }

        //get shift model
        foreach ($requestArray as $shift) {
            /** @var \App\Models\Shift $shiftModel */
            $shiftModel = $plant->onPlantDb()->shift()
                ->where('day_of_week', '=', $shift['day_of_week'])
                ->where('shift_type_id', '=', $shift['shift_type_id'])->first();

            if (!$shiftModel) {
                //somting wong: create new
                $shiftModel = new Shift();
                $shiftModel->plant_id = $plant->id;
                $shiftModel->day_of_week = $shift['day_of_week'];
                $shiftModel->shift_type_id = $shift['shift_type_id'];
                $shiftModel->setConnection($plant->getPlantConnection());
            }

            $shiftModel->start_time = $startTime->format('H:i');
            $shiftModel->enabled = $shift['enabled'];
            $shiftModel->setDuration($shift['end_time'], $shift['start_time'])
                ->setNormalDuration($shift['over_time'], $shift['start_time'])
                ->setOvertimeLimit()
                ->save();
        }

        //TODO: disable shift if it overlaps with other shift
        if (count($error_message) > 0) {
            return redirect()->back()->withErrors($error_message)->withInput();
        }

        $viewData = [
            'topBarTitle' => 'PLANT SETTINGS',
            'plant' => $plant,
            'shifts' => Shift::on($plantConnection)->where('plant_id', '=', $plant->id)->with('shiftType')->get(),
        ];

        return view('pages.web.plant-settings.shift.index', $viewData);
    }
}
