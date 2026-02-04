<?php

use App\Http\Controllers\Web\Admin\NetworkMonitoringController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;


require __DIR__ . '/admin/company.php';
require __DIR__ . '/admin/plant.php';
require __DIR__ . '/admin/user.php';
require __DIR__ . '/admin/opc-server.php';

Route::middleware('auth')->group(function () {
    Route::get('admin/network-monitoring', [NetworkMonitoringController::class, 'index'])
        ->name('admin.network-monitoring.index');

    Route::get('admin/log/1478963', function () {
        return response(File::get(storage_path('logs\laravel.log')), 200, [
            'Content-Type' => 'text/plain'
        ]);
    });
});
