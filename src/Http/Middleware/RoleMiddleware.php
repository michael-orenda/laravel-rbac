<?php

namespace MichaelOrenda\Rbac\Http\Middleware;

use Closure;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        $user = $request->user();

        if (!$user || !$user->hasRole($role)) {
            abort(403, 'Forbidden: Missing required role');
        }

        return $next($request);
    }
}
