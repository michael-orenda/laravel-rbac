<?php

namespace MichaelOrenda\Rbac\Traits;

use MichaelOrenda\Rbac\Models\Role;
use MichaelOrenda\Rbac\Models\Permission;

trait HasRoles
{
    public function roles()
    {
        return $this->morphToMany(
            Role::class,
            'model',
            'model_has_roles'
        );
    }

    public function permissions()
    {
        return $this->morphToMany(
            Permission::class,
            'model',
            'model_has_permissions'
        );
    }

    public function assignRole($role)
    {
        $roleId = $role instanceof Role ? $role->id : Role::slug($role)->value('id');
        return $this->roles()->syncWithoutDetaching([$roleId]);
    }

    public function revokeRole($role)
    {
        $roleId = $role instanceof Role ? $role->id : Role::slug($role)->value('id');
        return $this->roles()->detach($roleId);
    }

    public function hasRole(string $slug): bool
    {
        return $this->roles()->where('slug', $slug)->exists();
    }

    public function hasAnyRole(array $slugs): bool
    {
        return $this->roles()->whereIn('slug', $slugs)->exists();
    }

    public function hasPermission(string $slug): bool
    {
        if ($this->permissions()->where('slug', $slug)->exists()) {
            return true;
        }

        return $this->roles()
            ->whereHas('permissions', function ($q) use ($slug) {
                $q->where('slug', $slug);
            })
            ->exists();
    }

    public function hasAnyPermission(array $slugs): bool
    {
        return $this->permissions()->whereIn('slug', $slugs)->exists()
            || $this->roles()->whereHas('permissions', function ($q) use ($slugs) {
                $q->whereIn('slug', $slugs);
            })->exists();
    }
}
