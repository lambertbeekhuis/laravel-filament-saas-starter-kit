<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Middleware\AuthTenantMiddleware;
use App\Http\Middleware\PublicTenantMiddleware;
use App\Http\Middleware\IsSuperAdminMiddleware;
use App\Http\Controllers\AppController;

Route::view('/', 'welcome')
    ->name('home_all');

// public accessible page
Route::get('home/{tenant}', [AppController::class, 'home'])
    ->middleware([PublicTenantMiddleware::class])
    ->name('home_tenant');

// authenticated accessible page
Route::get('dashboard/{tenant?}', [AppController::class, 'dashboard'])
    ->middleware(['auth', 'verified', AuthTenantMiddleware::class])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth', 'verified', AuthTenantMiddleware::class])
    ->name('profile');

// make route to TestController
Route::get('/test/{type}', [TestController::class, 'test'])
    ->middleware(['auth', IsSuperAdminMiddleware::class]);

require __DIR__.'/auth.php';
