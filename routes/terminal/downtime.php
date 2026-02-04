<?php

use App\Http\Controllers\Terminal\DowntimeController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth.terminal','auth.terminal.operator']], function () {

    Route::get('terminal/{plant_uid}/{work_center_uid}/downtime', [DowntimeController::class, 'index'])
        ->name('terminal.downtime.index');


    //Ajax
    Route::post('terminal/{plant_uid}/{work_center_uid}/downtime/set/human-downtime', [DowntimeController::class, 'setHumanDowntime'])
        ->name('terminal.downtime.set.human-downtime');

    Route::post('terminal/{plant_uid}/{work_center_uid}/downtime/set/downtime-reason', [DowntimeController::class, 'setDowntimeReason'])
        ->name('terminal.downtime.set.downtime-reason');
});
