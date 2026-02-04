<?php

use App\Http\Controllers\Web\Analysis\QualityController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','auth.web']], function () {
    Route::get('analysis/quality/{plant_uid}/{work_center_uid?}', [QualityController::class, 'index'])
    ->name('analysis.quality');

    Route::post('analysis/quality/{plant_uid}', [QualityController::class, 'getData'])
    ->name('analysis.quality.get.data');
});