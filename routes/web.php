<?php

use Illuminate\Support\Facades\Route;


Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// ---- 需要登录的（安检门：admin.auth）----
Route::middleware('admin.auth')->group(function () {
    Route::get('/', [\App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:dashboard.view');

    // ---- 管理员管理：两层门禁（登录 + admin.manage 权限）----
    Route::middleware('permission:admin.manage')->group(function () {
        Route::resource('admins', \App\Http\Controllers\AdminController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    });
});

