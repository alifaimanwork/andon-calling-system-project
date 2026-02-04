<?php

use App\Http\Controllers\Web\Realtime\QualityController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','auth.web']], function () {
    Route::get('realtime/quality/{plant_uid}/{work_center_uid?}/{lineNo?}', [QualityController::class, 'index'])
        ->name('realtime.quality.index');
});
