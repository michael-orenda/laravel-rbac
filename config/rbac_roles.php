<?php

return [

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
            'hr-admin'      => 'HR Admin',
            'finance-admin' => 'Finance Admin',
            'project-admin' => 'Project Admin',
        ],
    ],

    'staff' => [
        'name' => 'Staff',
        'children' => [
            'accountant'    => 'Accountant',
            'sales-rep'     => 'Sales Rep',
            'support-agent' => 'Support Agent',
            'technician'    => 'Technician',
        ],
    ],

    'client' => [
        'name' => 'Client',
        'children' => [
            'vendor'       => 'Vendor',
            'partner'      => 'Partner',
            'supplier'     => 'Supplier',
            'tenant-admin' => 'Tenant Admin',
        ],
    ],

    'guest' => [
        'name' => 'Guest',
        'children' => [
            'trial-user'    => 'Trial User',
            'anonymous'     => 'Anonymous',
            'prospect-lead' => 'Prospect Lead',
        ],
    ],

];
