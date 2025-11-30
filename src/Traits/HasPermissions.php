<?php

namespace MichaelOrenda\Rbac\Traits;

use Illuminate\Support\Facades\Cache;
use MichaelOrenda\Rbac\Models\Role;
use MichaelOrenda\Rbac\Models\Permission;

trait HasPermissions
{
    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function permissions()
    {
        return $this->morphToMany(
            Permission::class,
            'model',
            'model_has_permissions',
            'model_id',
            'permission_id'
        );
    }

    public function roles()
    {
        return $this->morphToMany(
            Role::class,
            'model',
            'model_has_roles',
            'model_id',
            'role_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Permission Retrieval (with optional caching)
    |--------------------------------------------------------------------------
    */

    public function getAllPermissions(bool $fresh = false)
    {
        $cacheKey = "rbac.permissions.model.{$this->getMorphClass()}.{$this->id}";

        // bypass cache
        if ($fresh || !config('rbac.cache.enabled', false)) {
            return $this->calculateAllPermissions();
        }

        return Cache::remember(
            $cacheKey,
            config('rbac.cache.ttl', 3600),
            fn () => $this->calculateAllPermissions()
        );
    }

    protected function calculateAllPermissions()
    {
        // Direct permissions assigned to this model
        $direct = $this->permissions()->get();

        // Permissions inherited from roles
        $rolePermissions = $this->roles()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten();

        // Combine & remove duplicates
        return $direct->merge($rolePermissions)->unique('id')->values();
    }

    /*
    |--------------------------------------------------------------------------
    | Assignment Methods
    |--------------------------------------------------------------------------
    */

    public function assignPermission($permission)
    {
        $permissionId = $permission instanceof Permission
            ? $permission->id
            : Permission::slug($permission)->value('id');

        return $this->permissions()->syncWithoutDetaching([$permissionId]);
    }

    public function revokePermission($permission)
    {
        $permissionId = $permission instanceof Permission
            ? $permission->id
            : Permission::slug($permission)->value('id');

        return $this->permissions()->detach($permissionId);
    }

    public function syncPermissions(array $permissions)
    {
        $ids = collect($permissions)->map(function ($permission) {
            return $permission instanceof Permission
                ? $permission->id
                : Permission::slug($permission)->value('id');
        });

        return $this->permissions()->sync($ids->toArray());
    }

    /*
    |--------------------------------------------------------------------------
    | Role/Permission Checking
    |--------------------------------------------------------------------------
    */

    public function hasPermission(string $slug): bool
    {
        // Optional: Super admin bypass
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->getAllPermissions()
            ->contains(fn ($perm) => $perm->slug === $slug);
    }

    public function hasAnyPermission(array $slugs): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->getAllPermissions()
            ->contains(fn ($perm) => in_array($perm->slug, $slugs));
    }

    public function hasAllPermissions(array $slugs): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $available = $this->getAllPermissions()->pluck('slug')->toArray();

        return collect($slugs)->every(fn ($slug) => in_array($slug, $available));
    }

    /*
    |--------------------------------------------------------------------------
    | Role Checking (indirect permissions)
    |--------------------------------------------------------------------------
    */

    public function hasRole(string $slug): bool
    {
        return $this->roles()->where('slug', $slug)->exists();
    }

    public function hasAnyRole(array $slugs): bool
    {
        return $this->roles()->whereIn('slug', $slugs)->exists();
    }

    public function hasAllRoles(array $slugs): bool
    {
        return $this->roles()->whereIn('slug', $slugs)->count() === count($slugs);
    }

    /*
    |--------------------------------------------------------------------------
    | Super Admin Check
    |--------------------------------------------------------------------------
    */

    protected function isSuperAdmin(): bool
    {
        $role = config('rbac.super_admin_role', 'super-admin');

        return $this->hasRole($role);
    }

    /*
    |--------------------------------------------------------------------------
    | Cache Handling
    |--------------------------------------------------------------------------
    */

    public function clearPermissionCache()
    {
        $cacheKey = "rbac.permissions.model.{$this->getMorphClass()}.{$this->id}";
        Cache::forget($cacheKey);
    }
}
