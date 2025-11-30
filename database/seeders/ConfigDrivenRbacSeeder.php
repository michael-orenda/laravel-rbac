<?php

namespace MichaelOrenda\Rbac\Database\Seeders;

use Illuminate\Database\Seeder;

class ConfigDrivenRbacSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            ConfigRoleSeeder::class,
            ConfigPermissionSeeder::class,
            ConfigRolePermissionSeeder::class,
        ]);
    }
}
