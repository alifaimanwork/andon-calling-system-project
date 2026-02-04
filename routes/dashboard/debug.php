<?php

use App\Http\Controllers\Dashboard\DebugController;
use Illuminate\Support\Facades\Route;

Route::get('debug/{plant_uid}/{work_center_uid}', [DebugController::class, 'index'])
    ->name('debug.index');

Route::post('debug/{plant_uid}/{work_center_uid}/echo-test', [DebugController::class, 'echoTest'])
    ->name('debug.echo-test');
