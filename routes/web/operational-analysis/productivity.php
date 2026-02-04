<?php

use App\Http\Controllers\Web\Analysis\ProductivityController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','auth.web']], function () {
    Route::get('analysis/productivity/{plant_uid}/{work_center_uid?}', [ProductivityController::class, 'index'])
    ->name('analysis.productivity.index');

    Route::post('analysis/productivity/{plant_uid}', [ProductivityController::class, 'getData'])
    ->name('analysis.productivity.get.data');
});