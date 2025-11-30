<?php

namespace MichaelOrenda\Rbac\Database\Seeders;

use Illuminate\Database\Seeder;
use MichaelOrenda\Rbac\Models\Permission;
use MichaelOrenda\Rbac\Models\Role;

class ConfigRolePermissionSeeder extends Seeder
{
    public function run()
    {
        $mapping = config('rbac_role_permissions', []);

        foreach ($mapping as $roleSlug => $groups) {

            $role = Role::where('slug', $roleSlug)->first();

            if (! $role) continue;

            $permissionIds = collect($groups)
                ->flatMap(fn($group) => config("rbac_permissions.$group", []))
                ->map(fn($slug) => Permission::where('slug', $slug)->value('id'))
                ->filter()
                ->toArray();

            $role->permissions()->sync($permissionIds);
        }
    }
}
