<?php

use App\Http\Controllers\Web\PlantSelectionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('plant-selection', [PlantSelectionController::class, 'index'])
        ->name('plant-selection.index');
});
