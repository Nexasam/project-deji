<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'business_id', 'parent_role_id', 'name', 'slug', 'system_key', 'scope', 'description',
    'hierarchy_level', 'is_system', 'is_template', 'status',
    'created_by', 'updated_by',
])]
class Role extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
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

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
            ->withPivot('granted_by')
            ->withTimestamps();
    }

    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class, 'role_workspaces')
            ->withPivot(['access_level', 'granted_by'])
            ->withTimestamps();
    }

    public function membershipAssignments(): HasMany
    {
        return $this->hasMany(MembershipRoleAssignment::class);
    }

    public function directAssignments(): HasMany
    {
        return $this->hasMany(UserRoleAssignment::class);
    }
}
