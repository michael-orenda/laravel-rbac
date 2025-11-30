<?php

namespace MichaelOrenda\Rbac\Http\Middleware;

use Closure;

class PermissionMiddleware
{
    public function handle($request, Closure $next, $permission)
    {
        $user = $request->user();

        if (!$user || !$user->hasPermission($permission)) {
            abort(403, 'Forbidden: Missing required permission');
        }

        return $next($request);
    }
}
