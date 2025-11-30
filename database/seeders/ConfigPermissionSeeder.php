<?php

namespace MichaelOrenda\Rbac\Database\Seeders;

use Illuminate\Database\Seeder;
use MichaelOrenda\Rbac\Models\Permission;

class ConfigPermissionSeeder extends Seeder
{
    public function run()
    {
        $groups = config('rbac_permissions', []);

        foreach ($groups as $group => $permissions) {

            foreach ($permissions as $slug) {

                Permission::firstOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => ucwords(str_replace('-', ' ', $slug)),
                        'slug' => $slug
                    ]
                );
            }
        }
    }
}
