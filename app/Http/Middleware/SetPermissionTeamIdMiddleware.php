<?php

namespace App\Http\Middleware;

use App\Support\Features;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetPermissionTeamIdMiddleware
{
    /**
     * Fetch the tenant_id from the request and set tenant/team_id for Spatie roles/permissions.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Features::permissionsEnabled()) {
            setPermissionsTeamId($request->tenant);
        }

        return $next($request);
    }
}
