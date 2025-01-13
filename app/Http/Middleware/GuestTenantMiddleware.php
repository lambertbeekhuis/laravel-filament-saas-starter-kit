<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestTenantMiddleware
{
    /**
     * Gets the tenant from the url and injects it into the request
     *
     * $request->tenant
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (!$tenant_id = $request->route('tenant')) {
            abort(404, 'No tenant found in url');
        }

        $tenant = Tenant::query()
            ->where('id', $tenant_id)
            ->where('is_active', true)
            ->first();
        if (!$tenant) {
            abort(404, 'No tenant found');
        }

        $request->tenant = $tenant;

        return $next($request);
    }
}
