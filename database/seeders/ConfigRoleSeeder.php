<?php

namespace MichaelOrenda\Rbac\Database\Seeders;

use Illuminate\Database\Seeder;
use MichaelOrenda\Rbac\Models\Role;

class ConfigRoleSeeder extends Seeder
{
    public function run()
    {
        $roles = config('rbac_roles', []);

        foreach ($roles as $slug => $data) {

            $parent = Role::firstOrCreate(
                ['slug' => $slug],
                ['name' => $data['name'], 'slug' => $slug]
            );

            foreach ($data['children'] as $childSlug => $childName) {

                Role::firstOrCreate(
                    ['slug' => $childSlug],
                    [
                        'name' => $childName,
                        'slug' => $childSlug,
                        'parent_id' => $parent->id,
                    ]
                );
            }
        }
    }
}
