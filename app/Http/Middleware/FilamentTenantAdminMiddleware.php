<?php

namespace App\Http\Middleware;

use App\Support\Features;
use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Symfony\Component\HttpFoundation\Response;

class FilamentTenantAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Features::permissionsEnabled()) {
            return $next($request);
        }

        return app(PermissionMiddleware::class)
            ->handle($request, $next, 'admin', 'web');
    }
}
