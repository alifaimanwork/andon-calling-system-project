<?php

use App\Http\Controllers\Web\Realtime\DowntimeController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','auth.web']], function () {
    Route::get('realtime/downtime/{plant_uid}/{work_center_uid?}', [DowntimeController::class, 'index'])
        ->name('realtime.downtime.index');
});
