<?php

use App\Http\Controllers\OpcAdapterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/opc/configs', [OpcAdapterController::class, 'getConfigs']);
Route::post('/opc/events/tag-value-changed',[OpcAdapterController::class, 'tagValueChanged']);
Route::get('/opc/get-active-tags', [OpcAdapterController::class, 'getActiveTags'])->name('api.opc.get_active_tag');

Route::post('/opc/update-status', [OpcAdapterController::class, 'updateStatus'])->name('api.opc.update_status');
