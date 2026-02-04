<?php

use App\Http\Controllers\Web\Analysis\DowntimeController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','auth.web']], function () {
    Route::get('analysis/downtime/{plant_uid}/{workCenterUid?}', [DowntimeController::class, 'index'])
    ->name('analysis.downtime');

    Route::post('analysis/downtime/{plant_uid}', [DowntimeController::class, 'getData'])
    ->name('analysis.downtime.get.data');
});