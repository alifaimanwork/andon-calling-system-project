<?php

use App\Http\Controllers\Web\Admin\CompanyController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth','auth.superadmin']], function () {


    //Company Resource (index,create,store,show,edit,update,destroy)
    Route::get('admin/company', [CompanyController::class, 'index'])
        ->name('admin.company.index');

    Route::get('admin/company/create', [CompanyController::class, 'create'])
        ->name('admin.company.create');

    Route::post('admin/company', [CompanyController::class, 'store'])
        ->name('admin.company.store');

    Route::get('admin/company/{company}', [CompanyController::class, 'show'])
        ->name('admin.company.show');

    Route::put('admin/company/{company}', [CompanyController::class, 'update'])
        ->name('admin.company.update');

    Route::delete('admin/company/{company}', [CompanyController::class, 'destroy'])
        ->name('admin.company.destroy');

    Route::get('admin/company/{company}/edit', [CompanyController::class, 'edit'])
        ->name('admin.company.edit');


    //List datatable
    Route::post('admin/company/list', [CompanyController::class, 'datatable'])
        ->name('admin.company.list');
});

//Route::post('admin/company/list', [CompanyController::class, 'datatable'])
//link y perlu type utk access 
//->name('admin.company.list');
//tempat file disimpan
