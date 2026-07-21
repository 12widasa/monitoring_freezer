<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    return match (auth()->user()->role->value) {
        'admin' => redirect()->route('admin.dashboard'),
        'technician' => redirect()->route('technician.repairs'),
        'customer' => redirect()->route('customer.monitoring'),
        default => redirect()->route('login'),
    };
});


Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Portal Admin
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    });

    // Portal Teknisi
    Route::middleware('role:technician')->prefix('technician')->group(function () {
        Route::get('/repairs', fn() => 'Daftar Perbaikan Teknisi')->name('technician.repairs');
    });

    // Portal Pelanggan
    Route::middleware('role:customer')->prefix('customer')->group(function () {
        Route::get('/monitoring', fn() => 'Halaman Monitoring Pelanggan')->name('customer.monitoring');
    });
});
