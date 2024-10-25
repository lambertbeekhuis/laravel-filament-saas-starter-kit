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
        // this add auth()->client() to the auth-facade
        // @todo: caching this for the request
        Auth::macro('client', function () {
            $tenant = session('tenant', null);
            return $tenant ? Auth::user()?->clientsLastLogin($tenant)->first() : null;
        });
    }
}
