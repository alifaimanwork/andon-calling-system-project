<?php

use App\Http\Controllers\Web\Realtime\OeeController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','auth.web']], function () {

    Route::get('realtime/oee/{plant_uid}/{work_center_uid?}/{lineNo?}', [OeeController::class, 'index'])
        ->name('realtime.oee.index');
});
