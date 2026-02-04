<?php

use App\Http\Controllers\Web\Settings\NetworkStatusController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth','auth.plantadmin']], function () {

    //User Resource (index,create,store,show,edit,update,destroy)
    Route::get('settings/{plant_uid}/network-status', [NetworkStatusController::class, 'index'])
        ->name('settings.network-status.index');

    Route::get('settings/{plant_uid}/network-status/create', [NetworkStatusController::class, 'create'])
        ->name('settings.network-status.create');

    Route::post('settings/{plant_uid}/network-status', [NetworkStatusController::class, 'store'])
        ->name('settings.network-status.store');

    // Route::get('settings/{plant_uid}/network-status/{client_id}', [network-statusController::class, 'show'])
    //     ->name('settings.network-status.show');

    Route::get('settings/{plant_uid}/network-status/{client_id}/edit', [NetworkStatusController::class, 'edit'])
        ->name('settings.network-status.edit');

    Route::put('settings/{plant_uid}/network-status/{client_id}', [NetworkStatusController::class, 'update'])
        ->name('settings.network-status.update');

    Route::delete('settings/{plant_uid}/network-status/{client_id}', [NetworkStatusController::class, 'destroy'])
        ->name('settings.network-status.destroy');

    //List datatable
    Route::post('settings/{plant_uid}/network-status/list', [NetworkStatusController::class, 'datatable'])
        ->name('settings.network-status.list');
});
