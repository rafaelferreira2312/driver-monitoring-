<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DriverMonitoringController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
    if (! Auth::check()) {
        return redirect()->route('login.admin');
    }

    return redirect()->route(Auth::user()->role === User::ROLE_ADMIN ? 'admin.drivers.index' : 'driver.orders.index');
});

Route::get('/login/admin', [AuthController::class, 'admin'])->name('login.admin');
Route::get('/login', [AuthController::class, 'admin'])->name('login');
Route::get('/login/motorista', [AuthController::class, 'driver'])->name('login.driver');
Route::post('/login/{type}', [AuthController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/admin/motoristas', [DriverMonitoringController::class, 'admin'])->name('admin.drivers.index');
    Route::get('/motorista/pedidos', [DriverMonitoringController::class, 'driver'])->name('driver.orders.index');
    Route::patch('/pedidos/{order}', [DriverMonitoringController::class, 'updateOrder'])->name('orders.update');
});
