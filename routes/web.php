<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Middleware\SecureTenantMiddleware;
use App\Http\Middleware\IsSuperAdminMiddleware;
use App\Http\Controllers\AppController;

Route::view('/', 'welcome');

Route::get('dashboard/{tenant?}', [AppController::class, 'dashboard'])
    ->middleware(['auth', 'verified', SecureTenantMiddleware::class])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth', SecureTenantMiddleware::class])
    ->name('profile');

// make route to TestController
Route::get('/test/{type}', [TestController::class, 'test'])
    ->middleware(['auth', IsSuperAdminMiddleware::class]);

require __DIR__.'/auth.php';
