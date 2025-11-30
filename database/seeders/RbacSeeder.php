<?php

namespace MichaelOrenda\Rbac\Database\Seeders;

use Illuminate\Database\Seeder;

class RbacSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            ExtendedRoleSeeder::class,
            RolePermissionSeeder::class,
        ]);
    }
}
