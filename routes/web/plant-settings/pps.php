<?php

use App\Http\Controllers\Web\Settings\PpsController;
use App\Http\Controllers\Terminal\ProductionPlanningController;
use Illuminate\Support\Facades\Route;

//Route::post('settings.pps.create', [PpsController::class, 'submitForm']);

Route::group(['middleware' => ['auth','auth.plantadmin']], function () {

    //User Resource (index,create,store,show,edit,update,destroy)
    Route::get('settings/{plant_uid}/pps', [PpsController::class, 'index'])
        ->name('settings.pps.index');

    Route::post('settings/{plant_uid}/pps', [PpsController::class, 'downloadCSV'])
        ->name('settings.pps.csv');

    //Route::get('settings/{plant_uid}/pps/create', [PpsController::class, 'uploadCSV'])
    //    ->name('settings.pps.create');

    // Route::post('settings/{plant_uid}/shift', [ShiftController::class, 'store'])
    //     ->name('settings.shift.store');

    // Route::get('settings/{plant_uid}/shift/{shift_id}', [ShiftController::class, 'show'])
    //     ->name('settings.shift.show');

    // Route::get('settings/{plant_uid}/shift/{shift_id}/edit', [ShiftController::class, 'edit'])
    //     ->name('settings.shift.edit');

    //Route::put('settings/{plant_uid}/shift', [ShiftController::class, 'update'])
    //    ->name('settings.shift.update');

    // Route::delete('settings/{plant_uid}/shift/{shift_id}', [ShiftController::class, 'destroy'])
    //     ->name('settings.shift.destroy');
});
