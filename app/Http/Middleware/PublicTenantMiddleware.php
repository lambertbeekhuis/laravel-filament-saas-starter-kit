<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PublicTenantMiddleware
{
    /**
     * Access to a public page of a Tenant
     * The Tenant is taken from the url, and injects it into the request for re-use
     *
     * $request->tenant
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$tenant = Tenant::findOneForSlugOrId($request->route('tenant'))) {
            abort(404, 'Tenant not found ' . $request->route('tenant'));
        }
        $request->tenant = $tenant;

        return $next($request);
    }
}
