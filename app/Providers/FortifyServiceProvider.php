<?php

namespace App\Providers;

use App\Actions\Fortify\ResetUserPassword;
use App\Http\Responses\LoginResponse;
use App\Http\Responses\PasskeyLoginResponse;
use App\Http\Responses\TwoFactorLoginResponse;
use App\Http\Responses\VerifyEmailResponse;
use App\Listeners\RecordAuthenticatedTenantLogin;
use App\Models\User;
use Illuminate\Auth\Events\Attempting;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;
use Laravel\Fortify\Contracts\VerifyEmailResponse as VerifyEmailResponseContract;
use Laravel\Fortify\Fortify;
use Laravel\Passkeys\Contracts\PasskeyLoginResponse as PasskeyLoginResponseContract;
use Livewire\Livewire;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);
        $this->app->singleton(PasskeyLoginResponseContract::class, PasskeyLoginResponse::class);
        $this->app->singleton(TwoFactorLoginResponseContract::class, TwoFactorLoginResponse::class);
        $this->app->singleton(VerifyEmailResponseContract::class, VerifyEmailResponse::class);
    }

    public function boot(): void
    {
        $this->configureAuthentication();
        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();
        $this->configurePasswordConfirmation();

        Event::listen(Attempting::class, function (Attempting $event): void {
            $tenant = request()->input('tenant');

            if (is_string($tenant) && $tenant !== '') {
                session(['auth.tenant' => $tenant]);
            }
        });

        Event::listen(Login::class, RecordAuthenticatedTenantLogin::class);
    }

    private function configurePasswordConfirmation(): void
    {
        Livewire::addPersistentMiddleware([
            RequirePassword::class,
        ]);
    }

    private function configureActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);
    }

    private function configureAuthentication(): void
    {
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::query()
                ->where('email', Str::lower($request->input(Fortify::username())))
                ->first();

            if (! $user || ! Hash::check($request->input('password'), $user->password)) {
                return null;
            }

            if (! $user->is_active) {
                throw ValidationException::withMessages([
                    Fortify::username() => __('User is not active.'),
                ]);
            }

            $tenantKey = $request->session()->get('auth.tenant') ?? $request->query('tenant');

            if ($tenantKey && ! $user->tenantUsersLastLogin($tenantKey)->exists()) {
                throw ValidationException::withMessages([
                    Fortify::username() => __('No active organization found for this account.'),
                ]);
            }

            return $user;
        });
    }

    private function configureViews(): void
    {
        Fortify::loginView(function (Request $request) {
            $this->rememberTenantContext($request);

            return view('pages.auth.login', [
                'tenantSlug' => $request->query('tenant'),
            ]);
        });

        Fortify::verifyEmailView(fn () => view('pages.auth.verify-email'));
        Fortify::twoFactorChallengeView(fn () => view('pages.auth.two-factor-challenge'));
        Fortify::confirmPasswordView(fn () => view('pages.auth.confirm-password'));
        Fortify::resetPasswordView(fn () => view('pages.auth.reset-password'));
        Fortify::requestPasswordResetLinkView(fn () => view('pages.auth.forgot-password'));
    }

    private function rememberTenantContext(Request $request): void
    {
        $tenant = $request->query('tenant');

        if (! is_string($tenant) || $tenant === '') {
            return;
        }

        $request->session()->put('auth.tenant', $tenant);
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('passkeys', function (Request $request) {
            $credentialId = $request->input('credential.id');

            return Limit::perMinute(10)->by(
                ($credentialId ?: $request->session()->getId()).'|'.$request->ip(),
            );
        });
    }
}
