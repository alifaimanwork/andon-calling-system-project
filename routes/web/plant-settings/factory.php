<?php

use App\Http\Controllers\Web\Settings\FactoryController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth','auth.plantadmin']], function () {


    //User Resource (index,create,store,show,edit,update,destroy)
    Route::get('settings/{plant_uid}/factory', [FactoryController::class, 'index'])
        ->name('settings.factory.index');

    Route::get('settings/{plant_uid}/factory/create', [FactoryController::class, 'create'])
        ->name('settings.factory.create');

    Route::post('settings/{plant_uid}/factory', [FactoryController::class, 'store'])
        ->name('settings.factory.store');

    Route::get('settings/{plant_uid}/factory/{factory_id}', [FactoryController::class, 'show'])
        ->name('settings.factory.show');

    Route::get('settings/{plant_uid}/factory/{factory_id}/edit', [FactoryController::class, 'edit'])
        ->name('settings.factory.edit');

    Route::put('settings/{plant_uid}/factory/{factory_id}', [FactoryController::class, 'update'])
        ->name('settings.factory.update');

    Route::delete('settings/{plant_uid}/factory/{factory_id}', [FactoryController::class, 'destroy'])
        ->name('settings.factory.destroy');

    //List datatable
    Route::post('settings/{plant_uid}/factory/list', [FactoryController::class, 'datatable'])
        ->name('settings.factory.list');
});
