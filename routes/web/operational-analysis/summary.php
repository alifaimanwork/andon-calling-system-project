<?php

use App\Http\Controllers\Web\Analysis\SummaryController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','auth.web']], function () {
    Route::get('analysis/summary/{plant_uid}', [SummaryController::class, 'index'])
        ->name('analysis.summary.index');

    Route::post('analysis/summary/{plant_uid}', [SummaryController::class, 'getData'])
        ->name('analysis.summary.get.data');
});
