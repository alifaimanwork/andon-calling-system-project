<?php


use App\Http\Controllers\Terminal\ProductionPlanningController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth.terminal','auth.terminal.operator']], function () {

    Route::get('terminal/{plant_uid}/{work_center_uid}/production-planning', [ProductionPlanningController::class, 'index'])
        ->name('terminal.production-planning.index');

    //ajax
    Route::post('terminal/{plant_uid}/{work_center_uid}/production-planning/get/pps', [ProductionPlanningController::class, 'getPps'])
        ->name('terminal.production-planning.get.pps');

    Route::post('terminal/{plant_uid}/{work_center_uid}/production-planning/get/production-order', [ProductionPlanningController::class, 'getProductionOrder'])
        ->name('terminal.production-planning.get.production-order');

    Route::post('terminal/{plant_uid}/{work_center_uid}/production-planning/set/start-die-change', [ProductionPlanningController::class, 'setStartDieChange'])
        ->name('terminal.production-planning.set.start-die-change');
});
