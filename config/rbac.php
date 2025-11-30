<?php

return [
    'recursive_enabled' => true,
    'api_response_enabled' => true,
    
    /*
    |--------------------------------------------------------------------------
    | Auto-Seed RBAC Using Config Files
    |--------------------------------------------------------------------------
    |
    | When enabled, the package will automatically seed roles and permissions
    | based on `rbac_roles.php`, `rbac_permissions.php`, and
    | `rbac_role_permissions.php` after migrations are loaded.
    |
    | WARNING: Only use TRUE in local/testing. Leave FALSE in production to
    | prevent unwanted automatic modifications.
    |
    */
    'auto_seed' => false,
];
