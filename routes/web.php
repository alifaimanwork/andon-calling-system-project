<?php

use App\Http\Controllers\Terminal\TerminalController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TestETTController;
use App\Http\Controllers\TestOKNGController;
use Illuminate\Support\Facades\Route;
use App\Extras\ProductionPlanningSheet;
use App\Http\Controllers\Web\Admin\OpcServerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Route::group(['middleware' => ['auth']], function () {
//     Route::get('/dashboard', [TestController::class, 'dashboard'])->name('dashboard');
// });

Route::middleware('auth.terminal')->group(function () {
    //Generic route for terminal
    Route::get('terminal/{plant_uid}/{work_center_uid}', [TerminalController::class, 'index'])
        ->name('terminal.index');

    //Generic get current workcenter Data
    Route::post('terminal/{plant_uid}/{work_center_uid}', [TerminalController::class, 'getTerminalData'])
        ->name('terminal.get.data');
});

//IPOS Web
require __DIR__ . '/web/auth.php';
require __DIR__ . '/web/manage-account.php';
require __DIR__ . '/web/admin.php';

require __DIR__ . '/web/plant-selection.php';
require __DIR__ . '/web/overview.php';
require __DIR__ . '/web/realtime-monitoring.php';
require __DIR__ . '/web/operational-analysis.php';
require __DIR__ . '/web/plant-settings.php';

require __DIR__ . '/dashboard/debug.php';

Route::middleware('auth.terminal')->group(function () {
    //Generic route for terminal
    Route::get('terminal/{plant_uid}/{work_center_uid}', [TerminalController::class, 'index'])
        ->name('terminal.index');

    //Generic get current workcenter Data
    Route::post('terminal/{plant_uid}/{work_center_uid}', [TerminalController::class, 'getTerminalData'])
        ->name('terminal.get.data');
});

//Terminal
require __DIR__ . '/terminal/production-planning.php';

//Test OPC Tag
Route::middleware('auth')->get('/opc/test', [OpcServerController::class, 'test']);
Route::middleware('auth')->post('/opc/test', [OpcServerController::class, 'test'])->name('opc-tag-test');
Route::middleware('auth')->get('/opc/logs', [OpcServerController::class, 'logs']);
Route::middleware('auth')->post('/opc/logs/datatable', [OpcServerController::class, 'logDataset'])->name('opc-log-datatable');
