<?php

namespace App\Http\Middleware;

use App\Models\TenantUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureTenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // get tenant_id from request or from session
        $tenant_id_session = session('tenant', null);
        $tenant_id = $request->get('tenant', $tenant_id_session);

        $user = $request->user();

        // get this tenant, or the last logged-in tenant
        if (!$tenant = $user->authTenantForUser($tenant_id)) {
            abort(403);
        }

        // if tenant_id is not in session, update session and set last login
        if (!$tenant_id_session) {
            $tenant_id = $tenant->tenant_user->tenant_id;
            TenantUser::updateLastLoginForUserAndTenant($user->id, $tenant_id);
            session(['tenant' => $tenant->id]);
        }

        // inject into request
        $request->tenant = $tenant;

        return $next($request);
    }
}
