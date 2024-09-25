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
        // get tenant_id from request or from session
        $tenant_id_session = session('tenant', null);
        $tenant_id = $request->get('tenant', $tenant_id_session);

        $user = $request->user();

        // get this client, or the last logged-in client
        if (!$client = $user->clientsLastLogin($tenant_id)->first()) {
            abort(403);
        }

        // if tenant_id is not in session, update session and set last login
        if (!$tenant_id_session) {
            $client_id = $client->client_user->client_id;
            ClientUser::updateLastLoginForUserAndClient($user->id, $client_id);
            session(['tenant' => $client->id]);
        }

        // inject into request
        $request->tenant = $client;

        return $next($request);
    }
}
