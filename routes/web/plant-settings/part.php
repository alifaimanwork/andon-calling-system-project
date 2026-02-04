<?php

use App\Http\Controllers\Web\Settings\PartController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\File\Exception\PartialFileException;

Route::group(['middleware' => ['auth','auth.plantadmin']], function () {


    //User Resource (index,create,store,show,edit,update,destroy)
    Route::get('settings/{plant_uid}/part', [PartController::class, 'index'])
        ->name('settings.part.index');

    Route::get('settings/{plant_uid}/part/create', [PartController::class, 'create'])
        ->name('settings.part.create');

    Route::post('settings/{plant_uid}/part', [PartController::class, 'store'])
        ->name('settings.part.store');

    Route::get('settings/{plant_uid}/part/{viewUser}', [PartController::class, 'show'])
        ->name('settings.part.show');

    Route::put('settings/{plant_uid}/part/{viewUser}', [PartController::class, 'update'])
        ->name('settings.part.update');

    Route::delete('settings/{plant_uid}/part/{viewUser}', [PartController::class, 'destroy'])
        ->name('settings.part.destroy');

    Route::get('settings/{plant_uid}/part/{viewUser}/edit', [PartController::class, 'edit'])
        ->name('settings.part.edit');


    //List datatable
    Route::post('settings/{plant_uid}/part/list', [PartController::class, 'datatable'])
        ->name('settings.part.list');
});


