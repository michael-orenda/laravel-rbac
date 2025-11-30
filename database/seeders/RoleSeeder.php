<?php

namespace MichaelOrenda\Rbac\Database\Seeders;

use Illuminate\Database\Seeder;
use MichaelOrenda\Rbac\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'Super Admin', 'slug' => 'super-admin'],
            ['name' => 'Admin', 'slug' => 'admin'],
            ['name' => 'Staff', 'slug' => 'staff'],
            ['name' => 'Client', 'slug' => 'client'],
            ['name' => 'Guest', 'slug' => 'guest'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
