<?php


use App\Http\Controllers\Terminal\RejectController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth.terminal','auth.terminal.operator']], function () {

    Route::get('terminal/{plant_uid}/{work_center_uid}/reject', [RejectController::class, 'index'])
        ->name('terminal.reject.index');

    Route::post('terminal/{plant_uid}/{work_center_uid}/reject/set/reject', [RejectController::class, 'setReject'])
        ->name('terminal.reject.set.reject');
        
});
