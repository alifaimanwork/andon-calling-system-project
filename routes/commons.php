<?php

use App\Http\Controllers\CsrfController;
use App\Http\Controllers\NetClientController;
use Illuminate\Support\Facades\Route;


Route::post('/csrf', [CsrfController::class, 'getToken']);

Route::get('/network-client/{plant_uid}/{client_uid}/register', [NetClientController::class, 'register'])->name('network-client.register');
Route::post('/network-client/{plant_uid}/{client_uid}/report', [NetClientController::class, 'report'])->name('network-client.report');
