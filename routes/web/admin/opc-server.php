<?php

use App\Http\Controllers\Web\Admin\OpcServerController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth', 'auth.superadmin']], function () {


    //Company Resource (index,create,store,show,edit,update,destroy)
    Route::get('admin/opc-server', [OpcServerController::class, 'index'])
        ->name('admin.opc-server.index');

    Route::get('admin/opc-server/create', [OpcServerController::class, 'create'])
        ->name('admin.opc-server.create');

    Route::post('admin/opc-server', [OpcServerController::class, 'store'])
        ->name('admin.opc-server.store');

    Route::get('admin/opc-server/{opcServer}', [OpcServerController::class, 'show'])
        ->name('admin.opc-server.show');

    Route::put('admin/opc-server/{opcServer}', [OpcServerController::class, 'update'])
        ->name('admin.opc-server.update');

    Route::delete('admin/opc-server/{opcServer}', [OpcServerController::class, 'destroy'])
        ->name('admin.opc-server.destroy');

    Route::get('admin/opc-server/{opcServer}/edit', [OpcServerController::class, 'edit'])
        ->name('admin.opc-server.edit');


    //List datatable
    Route::post('admin/opc-server/list', [OpcServerController::class, 'datatable'])
        ->name('admin.opc-server.list');

    //active tag datatable
    Route::post('admin/opc-active-tag/list', [OpcServerController::class, 'activeTagDatatable'])
        ->name('admin.opc-active-tag.list');

    //Sync Tags
    Route::post('admin/opc-server/{opcServer}/sync', [OpcServerController::class, 'syncTags'])
        ->name('admin.opc-server.sync');

    Route::post('admin/opc-server/{opcServer}/force-resync', [OpcServerController::class, 'forceResyncOpcServer'])
        ->name('admin.opc-server.force-sync');

    Route::post('admin/opc-server/{opcServer}/assign-tags', [OpcServerController::class, 'assignTags'])->name('admin.opc-server.assign-tags');
});
