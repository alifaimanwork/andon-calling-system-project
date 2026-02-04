<?php

use App\Http\Controllers\Terminal\DieChangeController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth.terminal','auth.terminal.operator']], function () {

    Route::get('terminal/{plant_uid}/{work_center_uid}/die-change', [DieChangeController::class, 'index'])
        ->name('terminal.die-change.index');

    //ajax
    Route::post('terminal/{plant_uid}/{work_center_uid}/die-change/set/cancel-all-planning', [DieChangeController::class, 'setCancelDieChange'])
        ->name('terminal.die-change.set.cancel-all-planning');

    Route::post('terminal/{plant_uid}/{work_center_uid}/die-change/set/first-product-confirmation', [DieChangeController::class, 'setFirstProductConfirmation'])
        ->name('terminal.die-change.set.first-product-confirmation');
});
