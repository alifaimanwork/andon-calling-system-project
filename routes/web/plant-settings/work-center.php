<?php

use App\Http\Controllers\Web\Settings\WorkCenterController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth', 'auth.plantadmin']], function () {


    //User Resource (index,create,store,show,edit,update,destroy)
    Route::get('settings/{plant_uid}/work-center', [WorkCenterController::class, 'index'])
        ->name('settings.work-center.index');

    Route::get('settings/{plant_uid}/work-center/create', [WorkCenterController::class, 'create'])
        ->name('settings.work-center.create');

    Route::post('settings/{plant_uid}/work-center', [WorkCenterController::class, 'store'])
        ->name('settings.work-center.store');

    Route::get('settings/{plant_uid}/work-center/{workcenter_id}', [WorkCenterController::class, 'show'])
        ->name('settings.work-center.show');

    Route::get('settings/{plant_uid}/work-center/{workcenter_id}/edit', [WorkCenterController::class, 'edit'])
        ->name('settings.work-center.edit');

    Route::put('settings/{plant_uid}/work-center/{workcenter_id}', [WorkCenterController::class, 'update'])
        ->name('settings.work-center.update');

    Route::delete('settings/{plant_uid}/work-center/{workcenter_id}', [WorkCenterController::class, 'destroy'])
        ->name('settings.work-center.destroy');

    //List datatable
    Route::post('settings/{plant_uid}/work-center/list', [WorkCenterController::class, 'datatable'])
        ->name('settings.work-center.list');

    //Opc Tag datatable
    Route::post('settings/{plant_uid}/work-center/opc-tags', [WorkCenterController::class, 'opcTagDatatable'])
        ->name('settings.work-center.opc-tags.list');
});
