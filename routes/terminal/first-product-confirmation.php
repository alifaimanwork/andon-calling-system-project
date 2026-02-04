<?php

use App\Http\Controllers\Terminal\FirstProductConfirmationController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth.terminal','auth.terminal.operator']], function () {

    Route::get('terminal/{plant_uid}/{work_center_uid}/first-product-confirmation/', [FirstProductConfirmationController::class, 'index'])
        ->name('terminal.first-product-confirmation.index');

    Route::post('terminal/{plant_uid}/{work_center_uid}/first-product-confirmation/set/cancel-confirmation', [FirstProductConfirmationController::class, 'setCancelConfirmation'])
        ->name('terminal.first-product-confirmation.set.cancel-confirmation');

    Route::post('terminal/{plant_uid}/{work_center_uid}/first-product-confirmation/set/start-production', [FirstProductConfirmationController::class, 'setStartProduction'])
        ->name('terminal.first-product-confirmation.set.start-production');


    Route::post('terminal/{plant_uid}/{work_center_uid}/first-product-confirmation/set/reject-settings', [FirstProductConfirmationController::class, 'setRejectSettings'])
        ->name('terminal.first-product-confirmation.set.reject-settings');
});
