<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ─── Guest Routes ─────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/',      [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login', [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// ─── Authenticated Routes ─────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard (daily activity view)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Activity log updates (all authenticated users)
    Route::patch('/activity-logs/{activityLog}', [ActivityLogController::class, 'update'])
        ->name('activity-logs.update');

    Route::post('/activities/{activity}/quick-update', [ActivityLogController::class, 'quickUpdate'])
        ->name('activity-logs.quick-update');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/',                               [ReportController::class, 'index'])->name('index');
        Route::get('/export',                         [ReportController::class, 'export'])->name('export');
        Route::get('/activity/{activity}/history',    [ReportController::class, 'activityHistory'])->name('activity-history');
    });

    // Admin-only routes
    Route::middleware('can:admin')->group(function () {

        // Activity management
        Route::resource('activities', ActivityController::class)
            ->except(['show']);
        Route::post('/activities/{id}/restore', [ActivityController::class, 'restore'])
            ->name('activities.restore');

        // User management
        Route::resource('users', UserController::class)
            ->except(['show']);
    });
});
