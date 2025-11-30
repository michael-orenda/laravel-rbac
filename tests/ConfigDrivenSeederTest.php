<?php

namespace MichaelOrenda\Rbac\Tests;

use Orchestra\Testbench\TestCase;
use MichaelOrenda\Rbac\Models\Role;
use Illuminate\Support\Facades\Artisan;
use MichaelOrenda\Rbac\Models\Permission;
use MichaelOrenda\Rbac\RbacServiceProvider;

class ConfigDrivenSeederTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [RbacServiceProvider::class];
    }

    public function test_roles_are_generated_from_config()
    {
        config(['rbac_roles.super-admin.children.security-admin' => 'Security Admin']);

        Artisan::call('db:seed', [
            '--class' => \MichaelOrenda\Rbac\Database\Seeders\ConfigRoleSeeder::class
        ]);

        $this->assertDatabaseHas('roles', ['slug' => 'super-admin']);
        $this->assertDatabaseHas('roles', ['slug' => 'security-admin']);
    }

    public function test_permissions_are_generated_from_config()
    {
        config(['rbac_permissions.system' => ['manage-system', 'view-system-logs']]);

        Artisan::call('db:seed', [
            '--class' => \MichaelOrenda\Rbac\Database\Seeders\ConfigPermissionSeeder::class
        ]);

        $this->assertDatabaseHas('permissions', ['slug' => 'manage-system']);
    }

    public function test_role_permission_mapping_works()
    {
        config([
            'rbac_roles.admin.name' => 'Admin',
            'rbac_permissions.system' => ['manage-system'],
            'rbac_role_permissions.admin' => ['system'],
        ]);

        Artisan::call('db:seed', [
            '--class' => \MichaelOrenda\Rbac\Database\Seeders\ConfigDrivenRbacSeeder::class
        ]);

        $role = Role::where('slug', 'admin')->first();
        $permission = Permission::where('slug', 'manage-system')->first();

        $this->assertTrue($role->permissions->contains($permission));
    }
}
