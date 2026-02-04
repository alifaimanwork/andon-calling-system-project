<?php

use App\Http\Controllers\Web\Admin\PlantController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth','auth.superadmin']], function () {
    Route::get('admin/plant', [PlantController::class, 'index'])
        ->name('admin.plant.index');


    //Company Resource (index,create,store,show,edit,update,destroy)
    Route::get('admin/plant', [PlantController::class, 'index'])
        ->name('admin.plant.index');

    Route::get('admin/plant/create', [PlantController::class, 'create'])
        ->name('admin.plant.create');

    Route::post('admin/plant', [PlantController::class, 'store'])
        ->name('admin.plant.store');

    Route::get('admin/plant/{plant}', [PlantController::class, 'show'])
        ->name('admin.plant.show');

    Route::put('admin/plant/{plant}', [PlantController::class, 'update'])
        ->name('admin.plant.update');

    Route::delete('admin/plant/{plant}', [PlantController::class, 'destroy'])
        ->name('admin.plant.destroy');

    Route::get('admin/plant/{plant}/edit', [PlantController::class, 'edit'])
        ->name('admin.plant.edit');


    //List datatable
    Route::post('admin/plant/list', [PlantController::class, 'datatable'])
        ->name('admin.plant.list');
});
