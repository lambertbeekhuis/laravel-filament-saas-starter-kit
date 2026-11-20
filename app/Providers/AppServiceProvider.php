<?php

namespace App\Providers;

use App\Events\LoginEvent;
use App\Events\RegisteredTenantUser;
use App\Listeners\LoginSentryListener;
use App\Listeners\RegisteredTenantUserListener;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );

        // this add auth()->tenant() to the auth-facade
        Auth::macro('tenant', function () {
            $tenant = session('tenant', null);

            return $tenant ? Auth::user()?->authTenantForUser($tenant) : null;
        });

        Event::listen(LoginEvent::class, LoginSentryListener::class);
        Event::listen(RegisteredTenantUser::class, RegisteredTenantUserListener::class);
    }
}
