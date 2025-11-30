<?php

namespace MichaelOrenda\Rbac\Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Foundation\Auth\User;
use MichaelOrenda\Rbac\Models\Role;
use MichaelOrenda\Rbac\Http\Middleware\RoleMiddleware;
use MichaelOrenda\Rbac\Traits\HasRoles;
use Illuminate\Http\Request;

class MiddlewareRoleTest extends TestCase
{
    public function test_role_middleware_allows_access()
    {
        $middleware = new RoleMiddleware();

        $role = Role::create(['name' => 'Editor', 'slug' => 'editor']);

        $user = new class extends User {
            use HasRoles;
        };
        $user->id = 1;
        $user->assignRole('editor');

        $request = Request::create('/test');
        $request->setUserResolver(fn() => $user);

        $response = $middleware->handle($request, fn() => response('ok'), 'editor');

        $this->assertEquals('ok', $response->getContent());
    }
}
