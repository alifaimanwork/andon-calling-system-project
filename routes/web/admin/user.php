<?php

use App\Http\Controllers\Web\Admin\UserController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth','auth.superadmin']], function () {

    //Company Resource (index,create,store,show,edit,update,destroy)
    Route::get('admin/user', [UserController::class, 'index'])
        ->name('admin.user.index');

    Route::get('admin/user/create', [UserController::class, 'create'])
        ->name('admin.user.create');

    Route::post('admin/user', [UserController::class, 'store'])
        ->name('admin.user.store');

    Route::get('admin/user/{user}', [UserController::class, 'show'])
        ->name('admin.user.show');

    Route::put('admin/user/{user}', [UserController::class, 'update'])
        ->name('admin.user.update');

    Route::delete('admin/user/{user}', [UserController::class, 'destroy'])
        ->name('admin.user.destroy');

    Route::get('admin/user/{user}/edit', [UserController::class, 'edit'])
        ->name('admin.user.edit');

    Route::post('admin/user/{user}/edit/photo',[UserController::class,'updatePhoto'])->name('admin.user.edit.photo.update');
    Route::delete('admin/user/{user}/edit/photo',[UserController::class,'deletePhoto'])->name('admin.user.edit.photo.delete');

    //List datatable
    Route::post('admin/user/list', [UserController::class, 'datatable'])
        ->name('admin.user.list');
});
