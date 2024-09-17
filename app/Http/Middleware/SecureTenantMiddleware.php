<?php

namespace App\Http\Middleware;

use App\Models\ClientUser;
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
        $tenant_id = $request->get('tenant', session('tenant', null));

        $user = $request->user();

        if (!$client = $user->clientsLastLogin($tenant_id)->first()) {
            abort(403);
        }

        // Kind of exeption: if for some reason tenant_id is not set, set it to the last client
        if (!$tenant_id) {
            if ($clientUserLast = $user->clientUsersLastLogin(null)->first()) {
                $clientUserLast->update(['last_login_at' => now()]);
                session(['tenant' => $client->id]);
            }
        }

        // inject into request
        $request->tenant = $client;

        return $next($request);
    }
}
