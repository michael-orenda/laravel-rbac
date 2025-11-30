<?php

namespace MichaelOrenda\Rbac\Tests;

use Orchestra\Testbench\TestCase;
use MichaelOrenda\Rbac\Models\Role;
use MichaelOrenda\Rbac\RbacServiceProvider;

class RoleTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [RbacServiceProvider::class];
    }

    public function test_role_can_be_created()
    {
        $role = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin'
        ]);

        $this->assertDatabaseHas('roles', ['slug' => 'admin']);
    }
}
