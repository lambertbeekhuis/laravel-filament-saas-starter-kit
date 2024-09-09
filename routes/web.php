<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');


// make route to TestController
Route::get('/test/{type}', [TestController::class, 'test'])
    // @todo isSuperAdmin
    ->middleware(['auth', 'verified']);

require __DIR__.'/auth.php';
