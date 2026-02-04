<?php

use App\Http\Controllers\Terminal\TerminalController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.terminal')->group(function () {
    //Generic route for terminal
    Route::get('terminal/{plant_uid}/{work_center_uid}', [TerminalController::class, 'index'])
        ->name('terminal.index');

    //Generic get current workcenter Data
    Route::post('terminal/{plant_uid}/{work_center_uid}', [TerminalController::class, 'getTerminalData'])
        ->name('terminal.get.data');
});

//IPOS Terminals
require __DIR__ . '/terminal/auth.php';
require __DIR__ . '/terminal/production-planning.php';
require __DIR__ . '/terminal/die-change.php';
require __DIR__ . '/terminal/first-product-confirmation.php';
require __DIR__ . '/terminal/progress-status.php';
require __DIR__ . '/terminal/reject.php';
require __DIR__ . '/terminal/downtime.php';
require __DIR__ . '/terminal/pending.php';
require __DIR__ . '/terminal/rework.php';
