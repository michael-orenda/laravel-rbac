<?php

namespace MichaelOrenda\Rbac\Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Foundation\Auth\User;
use MichaelOrenda\Rbac\Models\Permission;
use MichaelOrenda\Rbac\Traits\HasPermissions;
use MichaelOrenda\Rbac\RbacServiceProvider;

class AssignPermissionTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [RbacServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('auth.providers.users.model', UserWithPermissions::class);
    }

    public function test_user_can_be_assigned_permission()
    {
        $permission = Permission::create([
            'name' => 'Delete Post',
            'slug' => 'delete-post'
        ]);

        $user = UserWithPermissions::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        $user->assignPermission('delete-post');

        $this->assertTrue($user->hasPermission('delete-post'));
    }
}

class UserWithPermissions extends User {
    use HasPermissions;
}
