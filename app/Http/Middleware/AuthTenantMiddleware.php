<?php

namespace App\Http\Middleware;

use App\Models\TenantUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthTenantMiddleware
{
    /**
     * Makes sure a Tenant is selected for authenticated requests.
     * Get the Tenant, first the url (tenant), second from the session, or last from the last selected tenant
     *
     * Injects the tenant into the request (request->tenant),
     * or available via auth()->tenant() (added to auth in the AppServiceProvider) )
     */
    public function handle(Request $request, Closure $next): Response
    {
        // get tenant_id from request or from session
        $tenant_id_session = session('tenant', null);
        $tenant_id = $request->route('tenant', $tenant_id_session);

        $user = $request->user();

        // get this tenant, or the last logged-in tenant (and cache it in user-object)
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
