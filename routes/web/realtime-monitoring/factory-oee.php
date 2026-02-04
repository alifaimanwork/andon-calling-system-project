<?php

use App\Http\Controllers\Web\Realtime\FactoryOeeController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','auth.web']], function () {
    Route::get('realtime/factory-oee/{plant_uid}/{factoryId?}', [FactoryOeeController::class, 'index'])
        ->name('realtime.factory-oee.index');
});
