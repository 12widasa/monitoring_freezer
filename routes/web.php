<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FreezerController;
use App\Http\Controllers\Admin\RepairController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    return match (auth()->user()->role->value) {
        'admin' => redirect()->route('admin.dashboard'),
        'technician' => redirect()->route('technician.dashboard'),
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
    Route::middleware(['active', 'role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        Route::get('/freezers', [FreezerController::class, 'index'])
            ->name('admin.freezers.index');

        Route::get('/freezers/{freezer}', [FreezerController::class, 'show'])
            ->name('admin.freezers.show');

        Route::post('/freezers', [FreezerController::class, 'store'])
            ->name('admin.freezers.store');

        Route::patch('/freezers/{freezer}', [FreezerController::class, 'update'])
            ->name('admin.freezers.update');

        Route::patch(
            '/freezers/{freezer}/verification',
            [FreezerController::class, 'verify'],
        )->name('admin.freezers.verification.update');

        Route::get('/repairs', [RepairController::class, 'index'])
            ->name('admin.repairs.index');

        Route::post('/repairs', [RepairController::class, 'store'])
            ->name('admin.repairs.store');

        Route::get('/repairs/{repair}', [RepairController::class, 'show'])
            ->name('admin.repairs.show');

        Route::patch(
            '/repairs/{repair}/technician',
            [RepairController::class, 'updateTechnician'],
        )->name('admin.repairs.technician.update');

        Route::get('/users', [UserController::class, 'index'])
            ->name('admin.users.index');

        Route::post('/users', [UserController::class, 'store'])
            ->name('admin.users.store');

        Route::patch('/users/{user}', [UserController::class, 'update'])
            ->name('admin.users.update');

        Route::patch('/users/{user}/status', [UserController::class, 'updateStatus'])
            ->name('admin.users.status.update');

        Route::get('/users/{user}', [UserController::class, 'show'])
            ->name('admin.users.show');
    });


    // Portal Teknisi
    Route::middleware(['active', 'role:technician'])
        ->prefix('technician')
        ->name('technician.')
        ->group(function () {
            Route::view('/dashboard', 'technician.dashboard')
                ->name('dashboard');

            Route::get('/repairs', fn() => 'Daftar Perbaikan Teknisi')
                ->name('repairs');

            Route::view('/repairs/{repair}', 'technician.repairs.show')
                ->name('repairs.show');
        });

    // Portal Pelanggan
    Route::middleware(['active', 'role:customer'])
        ->prefix('customer')
        ->name('customer.')
        ->group(function () {
            Route::view('/monitoring', 'customer.monitoring')
                ->name('monitoring');

            Route::view('/repairs/{repair}', 'customer.repairs.show')
                ->name('repairs.show');
        });
});
