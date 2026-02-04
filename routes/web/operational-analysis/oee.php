<?php

use App\Http\Controllers\Web\Analysis\OeeController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','auth.web']], function () {
    Route::get('analysis/oee/{plant_uid}/{work_center_uid?}', [OeeController::class, 'index'])
    ->name('analysis.oee.index');

    Route::post('analysis/oee/{plant_uid}', [OeeController::class, 'getData'])
    ->name('analysis.oee.get.data');
});