<?php

namespace App\Http\Responses\Concerns;

use App\Models\Tenant;
use Illuminate\Http\Request;

trait RedirectsToCurrentTenant
{
    protected function redirectPathForCurrentTenant(Request $request, string $suffix = ''): string
    {
        $tenant = $this->currentTenant($request);

        session(['tenant' => $tenant->slug]);

        return route('dashboard', ['tenant' => $tenant->slug]);
    }

    protected function currentTenant(Request $request): Tenant
    {
        $user = $request->user();

        abort_if(! $user, 403);

        $tenantKey = $request->session()->get('auth.tenant');

        $tenant = $user->tenantsLastLogin($tenantKey)->first()
            ?? $user->tenantsLastLogin()->first();

        abort_if(! $tenant, 403);

        return $tenant;
    }
}
