<?php

use App\Http\Controllers\Web\Settings\UserController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth', 'auth.plantadmin']], function () {


    //User Resource (index,create,store,show,edit,update,destroy)
    Route::get('settings/{plant_uid}/user', [UserController::class, 'index'])
        ->name('settings.user.index');

    Route::get('settings/{plant_uid}/user/create', [UserController::class, 'create'])
        ->name('settings.user.create');

    Route::post('settings/{plant_uid}/user', [UserController::class, 'store'])
        ->name('settings.user.store');

    Route::get('settings/{plant_uid}/user/{viewUser}', [UserController::class, 'show'])
        ->name('settings.user.show');

    Route::put('settings/{plant_uid}/user/{viewUser}', [UserController::class, 'update'])
        ->name('settings.user.update');

    Route::delete('settings/{plant_uid}/user/{viewUser}', [UserController::class, 'destroy'])
        ->name('settings.user.destroy');

    Route::get('settings/{plant_uid}/user/{viewUser}/edit', [UserController::class, 'edit'])
        ->name('settings.user.edit');

    Route::post('settings/{plant_uid}/user/{user}/edit/photo', [UserController::class, 'updatePhoto'])->name('settings.user.edit.photo.update');
    Route::delete('settings/{plant_uid}/user/{user}/edit/photo', [UserController::class, 'deletePhoto'])->name('settings.user.edit.photo.delete');

    //List datatable
    Route::post('settings/{plant_uid}/user/list', [UserController::class, 'datatable'])
        ->name('settings.user.list');
});
