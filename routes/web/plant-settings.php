<?php

use App\Http\Controllers\Web\Settings\BreakController;
use App\Http\Controllers\Web\Settings\DowntimeController;
use App\Http\Controllers\Web\Settings\DowntimeReasonController;
use App\Http\Controllers\Web\Settings\FactoryController;
use App\Http\Controllers\Web\Settings\NetworkStatusController;
use App\Http\Controllers\Web\Settings\PartController;
use App\Http\Controllers\Web\Settings\PpsController;
use App\Http\Controllers\Web\Settings\RejectTypeController;
use App\Http\Controllers\Web\Settings\ShiftController;
use App\Http\Controllers\Web\Settings\WorkCenterController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/plant-settings/user.php';
require __DIR__ . '/plant-settings/downtime.php';
require __DIR__ . '/plant-settings/downtime-reason.php';
require __DIR__ . '/plant-settings/part.php';
require __DIR__ . '/plant-settings/pps.php';
require __DIR__ . '/plant-settings/reject-type.php';
require __DIR__ . '/plant-settings/work-center.php';
require __DIR__ . '/plant-settings/factory.php';
require __DIR__ . '/plant-settings/shift.php';
require __DIR__ . '/plant-settings/break-schedules.php';
require __DIR__ . '/plant-settings/network-status.php';
Route::middleware('auth')->group(function () {


    // Route::get('settings/{plant_uid}/downtime-reason', [DowntimeReasonController::class, 'index'])
    //     ->name('settings.downtime-reason.index');

    // Route::get('settings/{plant_uid}/reject-type', [RejectTypeController::class, 'index'])
    //     ->name('settings.reject-type.index');

    // Route::get('settings/{plant_uid}/work-center', [WorkCenterController::class, 'index'])
    //     ->name('settings.work-center.index');

    // Route::get('settings/{plant_uid}/part', [PartController::class, 'index'])
    //     ->name('settings.part.index');

    // Route::get('settings/{plant_uid}/shift', [ShiftController::class, 'index'])
    //     ->name('settings.shift.index');

    // Route::get('settings/{plant_uid}/break', [BreakController::class, 'index'])
    //     ->name('settings.break.index');

    // Route::get('settings/{plant_uid}/factory', [FactoryController::class, 'index'])
    //     ->name('settings.factory.index');

    // Route::get('settings/{plant_uid}/network-status', [NetworkStatusController::class, 'index'])
    //     ->name('settings.network-status.index');
});

