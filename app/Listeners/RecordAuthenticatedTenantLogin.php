<?php

namespace App\Listeners;

use App\Events\LoginEvent;
use Illuminate\Auth\Events\Login;

class RecordAuthenticatedTenantLogin
{
    public function handle(Login $event): void
    {
        event(new LoginEvent($event->user));

        $tenantKey = session('auth.tenant');

        $tenantUser = $event->user->tenantUsersLastLogin($tenantKey)->first();

        if (! $tenantUser) {
            return;
        }

        $tenantUser->update(['last_login_at' => now()]);

        $tenant = $event->user->tenantsLastLogin($tenantKey)->first();

        if ($tenant) {
            session(['tenant' => $tenant->slug]);
        }
    }
}
