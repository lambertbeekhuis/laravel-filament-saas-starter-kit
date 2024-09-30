<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Middleware\SecureTenantMiddleware;
use App\Http\Controllers\AppController;

Route::view('/', 'welcome');

Route::get('dashboard/{tenant?}', [AppController::class, 'dashboard'])
    ->middleware(['auth', 'verified', SecureTenantMiddleware::class])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');


// make route to TestController
Route::get('/test/{type}', [TestController::class, 'test'])
    // @todo isSuperAdmin
    ->middleware(['auth', 'verified']);

require __DIR__.'/auth.php';
