<?php

use App\Http\Controllers\Terminal\ProgressStatusController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth.terminal','auth.terminal.operator']], function () {
    Route::get('terminal/{plant_uid}/{work_center_uid}/progress-status', [ProgressStatusController::class, 'index'])
        ->name('terminal.progress-status.index');

    //ajax
    Route::post('terminal/{plant_uid}/{work_center_uid}/progress-status/set/stop-production', [ProgressStatusController::class, 'setStopProduction'])
        ->name('terminal.progress-status.set.stop-production');

    Route::post('terminal/{plant_uid}/{work_center_uid}/progress-status/set/resume-production', [ProgressStatusController::class, 'setResumeProduction'])
        ->name('terminal.progress-status.set.resume-production');

    Route::post('terminal/{plant_uid}/{work_center_uid}/progress-status/set/break-production', [ProgressStatusController::class, 'setBreakProduction'])
        ->name('terminal.progress-status.set.break-production');

    // New routes for starting and stopping calling timers
    Route::post('terminal/{plant_uid}/{work_center_uid}/progress-status/start-calling-timer/{type}', [ProgressStatusController::class, 'startCallingTimer'])
        ->name('terminal.progress-status.start-calling-timer');

    Route::post('terminal/{plant_uid}/{work_center_uid}/progress-status/stop-calling-timer/{type}', [ProgressStatusController::class, 'stopCallingTimer'])
        ->name('terminal.progress-status.stop-calling-timer');

    Route::get('terminal/{plant_uid}/{work_center_uid}/progress-status/get-calling-states', [ProgressStatusController::class, 'getCallingStates'])
        ->name('terminal.progress-status.get-calling-states');
    
});
