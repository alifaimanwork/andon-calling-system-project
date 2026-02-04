<?php

use App\Http\Controllers\Web\Settings\DowntimeController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth','auth.plantadmin']], function () {


    //User Resource (index,create,store,show,edit,update,destroy)
    Route::get('settings/{plant_uid}/downtime', [DowntimeController::class, 'index'])
        ->name('settings.downtime.index');

    Route::get('settings/{plant_uid}/downtime/create', [DowntimeController::class, 'create'])
        ->name('settings.downtime.create');

    Route::post('settings/{plant_uid}/downtime', [DowntimeController::class, 'store'])
        ->name('settings.downtime.store');

    Route::get('settings/{plant_uid}/downtime/{downtime_id}', [DowntimeController::class, 'show'])
        ->name('settings.downtime.show');

    Route::get('settings/{plant_uid}/downtime/{downtime_id}/edit', [DowntimeController::class, 'edit'])
        ->name('settings.downtime.edit');

    Route::put('settings/{plant_uid}/downtime/{downtime_id}', [DowntimeController::class, 'update'])
        ->name('settings.downtime.update');

    Route::delete('settings/{plant_uid}/downtime/{downtime_id}', [DowntimeController::class, 'destroy'])
        ->name('settings.downtime.destroy');

    //List datatable
    Route::post('settings/{plant_uid}/downtime/list', [DowntimeController::class, 'datatable'])
        ->name('settings.downtime.list');
});
