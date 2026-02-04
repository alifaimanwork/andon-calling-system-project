<?php


use App\Http\Controllers\Terminal\ReworkController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth.terminal','auth.terminal.rework']], function () {

    Route::get('terminal/{plant_uid}/{work_center_uid}/rework', [ReworkController::class, 'index'])
        ->name('terminal.rework.index');

    Route::post('terminal/{plant_uid}/{work_center_uid}/rework/get/pending-rework', [ReworkController::class, 'getPendingRework'])
        ->name('terminal.rework.get.pending-rework');

    Route::post('terminal/{plant_uid}/{work_center_uid}/rework/set/rework', [ReworkController::class, 'setRework'])
        ->name('terminal.rework.set.rework');

    Route::post('terminal/{plant_uid}/{work_center_uid}/rework/set/close', [ReworkController::class, 'setClose'])
        ->name('terminal.rework.set.close');

    Route::post('terminal/{plant_uid}/{work_center_uid}/rework/set/unlock', [ReworkController::class, 'setUnlockRework'])
        ->name('terminal.rework.set.unlock');
        
});
