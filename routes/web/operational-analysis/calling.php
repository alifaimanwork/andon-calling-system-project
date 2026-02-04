<?php

use App\Http\Controllers\Web\Analysis\CallingController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','auth.web']], function () {
    Route::get('analysis/calling/{plant_uid}/{workCenterUid?}', [CallingController::class, 'index'])
        ->name('analysis.calling');

    Route::post('analysis/calling/{plant_uid}', [CallingController::class, 'getData'])
        ->name('analysis.calling.get.data');

    #Route::post('analysis/downtime/{plant_uid}', [DowntimeController::class, 'getData'])
    #->name('analysis.downtime.get.data');
});