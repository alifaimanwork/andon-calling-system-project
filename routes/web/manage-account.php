<?php

use App\Http\Controllers\Web\AccountController;

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('manage-account', [AccountController::class, 'index'])
        ->name('manage-account.index');

    Route::post('manage-account', [AccountController::class, 'update'])
        ->name('manage-account.update');

    Route::post('manage-account/picture', [AccountController::class, 'storePicture'])
        ->name('manage-account.picture.update');

    Route::delete('manage-account', [AccountController::class, 'destroyPicture'])
        ->name('manage-account.picture.destroy');
});
