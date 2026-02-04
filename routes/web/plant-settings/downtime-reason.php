<?php

use App\Http\Controllers\Web\Settings\DowntimeReasonController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth','auth.plantadmin']], function () {


    //User Resource (index,create,store,show,edit,update,destroy)
    Route::get('settings/{plant_uid}/downtime/{downtime_id}/downtime-reason', [DowntimeReasonController::class, 'index'])
        ->name('settings.downtime-reason.index');

    Route::get('settings/{plant_uid}/downtime/{downtime_id}/downtime-reason/create', [DowntimeReasonController::class, 'create'])
        ->name('settings.downtime-reason.create');

    Route::post('settings/{plant_uid}/downtime/{downtime_id}/downtime-reason', [DowntimeReasonController::class, 'store'])
        ->name('settings.downtime-reason.store');

    Route::get('settings/{plant_uid}/downtime/{downtime_id}/downtime-reason/{downtime_reason_id}', [DowntimeReasonController::class, 'show'])
        ->name('settings.downtime-reason.show');

    Route::put('settings/{plant_uid}/downtime/{downtime_id}/downtime-reason/{downtime_reason_id}', [DowntimeReasonController::class, 'update'])
        ->name('settings.downtime-reason.update');

    Route::delete('settings/{plant_uid}/downtime/{downtime_id}/downtime-reason/{downtime_reason_id}', [DowntimeReasonController::class, 'destroy'])
        ->name('settings.downtime-reason.destroy');

    Route::get('settings/{plant_uid}/downtime/{downtime_id}/downtime-reason/{downtime_reason_id}/edit', [DowntimeReasonController::class, 'edit'])
        ->name('settings.downtime-reason.edit');


    //List datatable

    Route::post('settings/{plant_uid}/downtime/reason-list/downtime-reason/list', [DowntimeReasonController::class, 'datatable'])
        ->name('settings.downtime-reason.list');
});
