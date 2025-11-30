<?php

namespace MichaelOrenda\Rbac\Database\Seeders;

use Illuminate\Database\Seeder;
use MichaelOrenda\Rbac\Models\Role;

class ExtendedRoleSeeder extends Seeder
{
    public function run()
    {
        $hierarchy = [
            'super-admin' => [
                'name' => 'Super Admin',
                'children' => [
                    'system-auditor' => 'System Auditor',
                    'security-admin' => 'Security Admin',
                ],
            ],
            'admin' => [
                'name' => 'Admin',
                'children' => [
                    'hr-admin' => 'HR Admin',
                    'finance-admin' => 'Finance Admin',
                    'project-admin' => 'Project Admin',
                ],
            ],
            'staff' => [
                'name' => 'Staff',
                'children' => [
                    'accountant' => 'Accountant',
                    'sales-rep' => 'Sales Rep',
                    'support-agent' => 'Support Agent',
                    'technician' => 'Technician',
                ],
            ],
            'client' => [
                'name' => 'Client',
                'children' => [
                    'vendor' => 'Vendor',
                    'partner' => 'Partner',
                    'supplier' => 'Supplier',
                    'tenant-admin' => 'Tenant Admin',
                ],
            ],
            'guest' => [
                'name' => 'Guest',
                'children' => [
                    'trial-user' => 'Trial User',
                    'anonymous' => 'Anonymous',
                    'prospect-lead' => 'Prospect Lead',
                ],
            ],
        ];

        foreach ($hierarchy as $slug => $data) {
            $parent = Role::firstOrCreate(['slug' => $slug], [
                'name' => $data['name'], 'slug' => $slug
            ]);

            foreach ($data['children'] as $cSlug => $cName) {
                Role::firstOrCreate(
                    ['slug' => $cSlug],
                    ['name' => $cName, 'slug' => $cSlug, 'parent_id' => $parent->id]
                );
            }
        }
    }
}
