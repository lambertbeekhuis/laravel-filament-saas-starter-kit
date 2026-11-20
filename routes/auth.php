<?php

use App\Http\Middleware\PublicTenantMiddleware;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('register/{tenant}/{token?}', 'pages.auth.register')
    ->name('register')
    ->middleware(PublicTenantMiddleware::class);

Route::middleware('guest')->group(function () {
    Volt::route('register-tenant', 'pages.auth.register-tenant')
        ->name('register.tenant');
});
