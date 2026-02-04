<?php

use App\Http\Controllers\Web\Settings\ShiftController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth','auth.plantadmin']], function () {


    //User Resource (index,create,store,show,edit,update,destroy)
    Route::get('settings/{plant_uid}/shift', [ShiftController::class, 'index'])
        ->name('settings.shift.index');

    Route::get('settings/{plant_uid}/shift/create', [ShiftController::class, 'create'])
        ->name('settings.shift.create');

    // Route::post('settings/{plant_uid}/shift', [ShiftController::class, 'store'])
    //     ->name('settings.shift.store');

    // Route::get('settings/{plant_uid}/shift/{shift_id}', [ShiftController::class, 'show'])
    //     ->name('settings.shift.show');

    // Route::get('settings/{plant_uid}/shift/{shift_id}/edit', [ShiftController::class, 'edit'])
    //     ->name('settings.shift.edit');

    Route::put('settings/{plant_uid}/shift', [ShiftController::class, 'update'])
        ->name('settings.shift.update');

    // Route::delete('settings/{plant_uid}/shift/{shift_id}', [ShiftController::class, 'destroy'])
    //     ->name('settings.shift.destroy');
});
