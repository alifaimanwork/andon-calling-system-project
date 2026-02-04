<?php

use App\Http\Controllers\Web\Settings\BreakScheduleController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth','auth.plantadmin']], function () {


    //User Resource (index,create,store,show,edit,update,destroy)
    Route::get('settings/{plant_uid}/break-schedule', [BreakScheduleController::class, 'index'])
        ->name('settings.break-schedule.index');

    Route::get('settings/{plant_uid}/break-schedule/create', [BreakScheduleController::class, 'create'])
        ->name('settings.break-schedule.create');

    Route::post('settings/{plant_uid}/break-schedule', [BreakScheduleController::class, 'store'])
        ->name('settings.break-schedule.store');

    Route::get('settings/{plant_uid}/break-schedule/{break_schedule_id}', [BreakScheduleController::class, 'show'])
        ->name('settings.break-schedule.show');

    Route::get('settings/{plant_uid}/break-schedule/{break_schedule_id}/edit', [BreakScheduleController::class, 'edit'])
        ->name('settings.break-schedule.edit');

    Route::put('settings/{plant_uid}/break-schedule/{break_schedule_id}', [BreakScheduleController::class, 'update'])
        ->name('settings.break-schedule.update');

    Route::delete('settings/{plant_uid}/break-schedule/{break_schedule_id}', [BreakScheduleController::class, 'destroy'])
        ->name('settings.break-schedule.destroy');

    //List datatable
    Route::post('settings/{plant_uid}/break-schedule/list', [BreakScheduleController::class, 'datatable'])
        ->name('settings.break-schedule.list');
});
