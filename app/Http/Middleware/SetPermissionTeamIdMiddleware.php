<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetPermissionTeamIdMiddleware
{
    /**
     * Fetch the tenant_id from the request and set tenant/team_id for the Spatie Roles/Permissions
     */
    public function handle(Request $request, Closure $next): Response
    {
        setPermissionsTeamId($request->tenant); // is the tenant-id from the route
        return $next($request);
    }
}
