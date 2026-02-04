<?php
use App\Http\Controllers\Web\Settings\RejectTypeController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth','auth.plantadmin']], function () {

    //User Resource (index,create,store,show,edit,update,destroy)
    Route::get('settings/{plant_uid}/reject-type', [RejectTypeController::class, 'index'])
        ->name('settings.reject-type.index');

    Route::get('settings/{plant_uid}/reject-type/create/{group_id}', [RejectTypeController::class, 'create'])
        ->name('settings.reject-type.create');

    Route::post('settings/{plant_uid}/reject-type', [RejectTypeController::class, 'store'])
        ->name('settings.reject-type.store');

    Route::get('settings/{plant_uid}/reject-type/{reject_id}', [RejectTypeController::class, 'show'])
        ->name('settings.reject-type.show');

    Route::get('settings/{plant_uid}/reject-type/{reject_id}/edit', [RejectTypeController::class, 'edit'])
        ->name('settings.reject-type.edit');

    Route::put('settings/{plant_uid}/reject-type/{reject_id}', [RejectTypeController::class, 'update'])
        ->name('settings.reject-type.update');

    Route::delete('settings/{plant_uid}/reject-type/{reject_id}', [RejectTypeController::class, 'destroy'])
        ->name('settings.reject-type.destroy');

    //List datatable
    Route::post('settings/{plant_uid}/reject-type/list/{group_id}', [RejectTypeController::class, 'datatable'])
        ->name('settings.reject-type.list');
});
