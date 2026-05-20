<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\SwitchAccountController;

// Auth routes (dari Breeze)
require __DIR__.'/auth.php';

// Semua route butuh login
Route::middleware(['auth'])->group(function () {

    // Dashboard (semua role bisa akses)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ── Admin only ─────────────────────────────────────
    Route::middleware(['role:admin'])->group(function () {

        // Kendaraan
        Route::resource('vehicles', VehicleController::class);

        // Driver
        Route::resource('drivers', DriverController::class);

        // Pemesanan
        Route::resource('bookings', BookingController::class);
        Route::post('bookings/{booking}/complete', [BookingController::class, 'complete'])
            ->name('bookings.complete');
    });

    // ── Admin & Manager bisa approve ───────────────────
    Route::middleware(['role:admin,manager'])->group(function () {
        Route::get('approvals', [ApprovalController::class, 'index'])
            ->name('approvals.index');
        Route::get('approvals/{approval}', [ApprovalController::class, 'show'])
            ->name('approvals.show');
        Route::post('approvals/{approval}/approve', [ApprovalController::class, 'approve'])
            ->name('approvals.approve');
        Route::post('approvals/{approval}/reject', [ApprovalController::class, 'reject'])
            ->name('approvals.reject');
        Route::get('logs', [App\Http\Controllers\LogController::class, 'index'])
            ->name('logs.index');
        Route::post('switch-account/{user}', [App\Http\Controllers\SwitchAccountController::class, 'switch'])
            ->name('switch.account');

        // Laporan
        Route::get('reports', [ReportController::class, 'index'])
            ->name('reports.index');
        Route::get('reports/export', [ReportController::class, 'export'])
            ->name('reports.export');
    });
});