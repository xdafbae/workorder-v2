<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RuleController;
use App\Http\Controllers\WorkOrderController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/demo-login/{role}', [AuthController::class, 'demo'])
    ->whereIn('role', ['nurse', 'technician', 'admin', 'super_admin'])
    ->name('demo.login');

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return match (auth()->user()->role) {
            'technician' => redirect()->route('dashboard.technician'),
            'admin', 'super_admin' => redirect()->route('dashboard.admin'),
            default => redirect()->route('dashboard.nurse'),
        };
    })->name('home');

    Route::get('/dashboard/perawat', [DashboardController::class, 'nurse'])
        ->middleware('role:nurse,admin,super_admin')
        ->name('dashboard.nurse');

    Route::get('/dashboard/teknisi', [DashboardController::class, 'technician'])
        ->middleware('role:technician,admin,super_admin')
        ->name('dashboard.technician');

    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])
        ->middleware('role:admin,super_admin')
        ->name('dashboard.admin');

    Route::get('/work-orders/create', [WorkOrderController::class, 'create'])
        ->middleware('role:nurse,admin,super_admin')
        ->name('work-orders.create');
    Route::post('/work-orders', [WorkOrderController::class, 'store'])
        ->middleware('role:nurse,admin,super_admin')
        ->name('work-orders.store');
    Route::get('/work-orders/{workOrder}', [WorkOrderController::class, 'show'])->name('work-orders.show');
    Route::patch('/work-orders/{workOrder}', [WorkOrderController::class, 'update'])
        ->middleware('role:technician,admin,super_admin')
        ->name('work-orders.update');

    Route::get('/devices/print-qr', [DeviceController::class, 'printQr'])
        ->middleware('role:admin,super_admin')
        ->name('devices.print-qr');
    Route::resource('devices', DeviceController::class)->only(['index', 'store', 'update', 'destroy'])
        ->middleware('role:admin,super_admin');

    Route::post('/units', [\App\Http\Controllers\UnitController::class, 'store'])
        ->middleware('role:admin,super_admin')
        ->name('units.store');

    Route::get('/admin/rules', [RuleController::class, 'index'])
        ->middleware('role:admin,super_admin')
        ->name('rules.index');
    Route::post('/admin/rules', [RuleController::class, 'store'])
        ->middleware('role:admin,super_admin')
        ->name('rules.store');
    Route::patch('/admin/rules/{rule}', [RuleController::class, 'update'])
        ->middleware('role:admin,super_admin')
        ->name('rules.update');
    Route::delete('/admin/rules/{rule}', [RuleController::class, 'destroy'])
        ->middleware('role:super_admin')
        ->name('rules.destroy');

    Route::get('/reports', [ReportController::class, 'index'])
        ->middleware('role:admin,super_admin')
        ->name('reports.index');
    Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])
        ->middleware('role:admin,super_admin')
        ->name('reports.export-csv');
});
