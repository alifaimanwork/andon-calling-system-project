<?php

use App\Http\Controllers\Web\Analysis\DprController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'auth.web']], function () {
    Route::get('analysis/dpr/{plant_uid}/{workCenterUid?}', [DprController::class, 'index'])
        ->name('analysis.dpr');


    // AJAX
    Route::post('analysis/dpr/{plant_uid}/get/productions', [DprController::class, 'getProductions'])
        ->name('analysis.dpr.get.productions');

    Route::post('analysis/dpr/{plant_uid}/get/dpr-data', [DprController::class, 'getDprData'])
        ->name('analysis.dpr.get.dprdata');

    // EXPORT
    Route::get('analysis/dpr/{plant_uid}/{work_center_uid}/{production_id}/export', [DprController::class, 'exportDprData'])
        ->name('analysis.dpr.export');
});
