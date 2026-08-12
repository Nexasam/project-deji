<?php

namespace App\Models;

use App\Enums\RoleScope;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'business_id', 'parent_role_id', 'name', 'slug', 'system_key', 'scope',
    'description', 'hierarchy_level', 'is_system', 'is_template', 'status',
])]
class Role extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'scope' => RoleScope::class,
            'hierarchy_level' => 'integer',
            'is_system' => 'boolean',
            'is_template' => 'boolean',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_role_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_role_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
            ->withPivot(['id', 'status'])
            ->withTimestamps();
    }

    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class, 'role_workspaces')
            ->withPivot(['id', 'access_level', 'status'])
            ->withTimestamps();
    }
}
