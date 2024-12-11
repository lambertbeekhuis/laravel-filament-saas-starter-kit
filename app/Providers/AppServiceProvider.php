<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // this add auth()->tenant() to the auth-facade
        Auth::macro('tenant', function () {
            $tenant = session('tenant', null);
            return $tenant ? Auth::user()?->authTenantForUser($tenant) : null;
        });
    }
}
