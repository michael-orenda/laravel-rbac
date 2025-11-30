<?php

namespace MichaelOrenda\Rbac\Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Foundation\Auth\User;
use MichaelOrenda\Rbac\Models\Role;
use MichaelOrenda\Rbac\Traits\HasRoles;
use MichaelOrenda\Rbac\RbacServiceProvider;

class AssignRoleTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [RbacServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('auth.providers.users.model', UserWithRoles::class);
    }

    public function test_user_can_be_assigned_role()
    {
        $role = Role::create([
            'name' => 'Manager',
            'slug' => 'manager'
        ]);

        $user = UserWithRoles::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        $user->assignRole('manager');

        $this->assertTrue($user->hasRole('manager'));
    }
}

class UserWithRoles extends User {
    use HasRoles;
}
