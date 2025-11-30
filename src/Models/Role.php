<?php

namespace MichaelOrenda\Rbac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Role extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
    ];

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'permission_role',
            'role_id',
            'permission_id'
        );
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function assignedModels()
    {
        return $this->morphedByMany(null, 'model', 'model_has_roles');
    }

    public function scopeSlug(Builder $query, string $slug)
    {
        return $query->where('slug', $slug);
    }

    public function scopeSearch(Builder $query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('slug', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    public function scopeRoot(Builder $query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeChildrenOf(Builder $query, int $parentId)
    {
        return $query->where('parent_id', $parentId);
    }

    public function isRecursiveEnabled(): bool
    {
        return config('rbac.recursive_enabled', true) &&
               trait_exists(\MichaelOrenda\Recursive\Traits\HasRecursiveRelations::class);
    }

    public function hasPermission(string $permissionSlug): bool
    {
        return $this->permissions()
            ->where('slug', $permissionSlug)
            ->exists();
    }

    public function allPermissions()
    {
        if ($this->isRecursiveEnabled() && method_exists($this, 'descendants')) {
            return $this->permissions
                ->merge($this->descendants->pluck('permissions')->flatten())
                ->unique('id');
        }

        return $this->permissions;
    }
}
