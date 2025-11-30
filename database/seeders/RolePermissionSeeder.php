<?php

namespace MichaelOrenda\Rbac\Database\Seeders;

use Illuminate\Database\Seeder;
use MichaelOrenda\Rbac\Models\Permission;
use MichaelOrenda\Rbac\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        $admin = Role::where('slug', 'admin')->first();
        $staff = Role::where('slug', 'staff')->first();
        $client = Role::where('slug', 'client')->first();
        $guest = Role::where('slug', 'guest')->first();

        $allPermissions = Permission::pluck('id')->toArray();

        if ($admin) $admin->permissions()->sync($allPermissions);

        if ($staff) {
            $staff->permissions()->sync(
                Permission::whereIn('slug', [
                    'view-orders','create-orders','update-orders','view-users'
                ])->pluck('id')->toArray()
            );
        }

        if ($client) {
            $client->permissions()->sync(
                Permission::whereIn('slug', ['view-products','view-public-pages'])
                ->pluck('id')->toArray()
            );
        }

        if ($guest) {
            $guest->permissions()->sync(
                Permission::whereIn('slug', ['view-products','view-public-pages'])
                ->pluck('id')->toArray()
            );
        }
    }
}
