<?php

use App\Http\Controllers\Web\Analysis\FactoryOeeController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','auth.web']], function () {

    Route::get('analysis/factory-oee/{plant_uid}/{factoryId?}', [FactoryOeeController::class, 'index'])
        ->name('analysis.factory-oee.index');

    Route::post('analysis/factory-oee/{plant_uid}/{factoryId?}', [FactoryOeeController::class, 'getData'])
        ->name('analysis.factory-oee.get.data');
});
