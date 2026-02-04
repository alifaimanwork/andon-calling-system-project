<?php

use App\Http\Controllers\Terminal\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest.terminal')->group(function () {
    Route::get('terminal/{plant_uid}/{work_center_uid}/login', [AuthenticatedSessionController::class, 'create'])
        ->name('terminal.login');

    Route::post('terminal/{plant_uid}/{work_center_uid}/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth.terminal')->group(function () {

    Route::get('terminal/{plant_uid}/{work_center_uid}/logout', [AuthenticatedSessionController::class, 'logout']);
    Route::post('terminal/{plant_uid}/{work_center_uid}/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('terminal.logout');
});
