<?php

use App\Http\Controllers\Terminal\PendingController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth.terminal','auth.terminal.operator']], function () {

    Route::get('terminal/{plant_uid}/{work_center_uid}/pending', [PendingController::class, 'index'])
        ->name('terminal.pending.index');

    Route::post('terminal/{plant_uid}/{work_center_uid}/pending/set/pending', [PendingController::class, 'setPending'])
        ->name('terminal.pending.set.pending');
});
