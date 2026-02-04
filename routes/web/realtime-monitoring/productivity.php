<?php

use App\Http\Controllers\Web\Realtime\ProductivityController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','auth.web']], function () {
    Route::get('realtime/productivity/{plant_uid}/{work_center_uid?}/{lineNo?}', [ProductivityController::class, 'index'])
        ->name('realtime.productivity.index');
});
