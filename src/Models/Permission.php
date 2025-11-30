<?php

namespace MichaelOrenda\Rbac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id'
    ];

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'permission_role',
            'permission_id',
            'role_id'
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
        return $this->morphedByMany(
            null,
            'model',
            'model_has_permissions'
        );
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

    public function isAssignedTo(string $modelType, int $modelId): bool
    {
        return $this->assignedModels()
            ->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->exists();
    }
}
