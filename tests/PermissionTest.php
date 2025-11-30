<?php

namespace MichaelOrenda\Rbac\Tests;

use Orchestra\Testbench\TestCase;
use MichaelOrenda\Rbac\Models\Permission;
use MichaelOrenda\Rbac\RbacServiceProvider;

class PermissionTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [RbacServiceProvider::class];
    }

    public function test_permission_can_be_created()
    {
        $permission = Permission::create([
            'name' => 'Edit Users',
            'slug' => 'edit-users'
        ]);

        $this->assertDatabaseHas('permissions', ['slug' => 'edit-users']);
    }
}
