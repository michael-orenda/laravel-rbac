<?php

namespace MichaelOrenda\Rbac\Database\Seeders;

use Illuminate\Database\Seeder;
use MichaelOrenda\Rbac\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'manage-system','view-system-logs','manage-settings',
            'view-users','create-users','update-users','delete-users',
            'assign-roles','assign-permissions',
            'view-roles','create-roles','update-roles','delete-roles',
            'view-permissions','create-permissions','update-permissions','delete-permissions',
            'view-public-pages','view-products','create-account',
            'view-orders','create-orders','update-orders','delete-orders',
        ];

        foreach ($permissions as $slug) {
            Permission::firstOrCreate(
                ['slug' => $slug],
                ['name' => ucwords(str_replace('-', ' ', $slug)), 'slug' => $slug]
            );
        }
    }
}
