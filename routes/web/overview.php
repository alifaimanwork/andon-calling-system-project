<?php

use App\Http\Controllers\Web\OverviewController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth','auth.web']], function () {
    Route::get('overview/{plant_uid}', [OverviewController::class, 'index'])
        ->name('overview.index');
});
