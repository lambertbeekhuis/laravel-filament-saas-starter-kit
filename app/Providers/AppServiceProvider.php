<?php

namespace App\Providers;

use App\Events\LoginEvent;
use App\Events\RegisteredTenantUser;
use App\Listeners\LoginSentryListener;
use App\Listeners\RegisteredTenantUserListener;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
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

        Event::listen(LoginEvent::class, LoginSentryListener::class);
        Event::listen(RegisteredTenantUser::class, RegisteredTenantUserListener::class);
    }
}
