<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'workspace_id', 'name', 'category', 'audience', 'module', 'action',
    'description', 'risk_level', 'requires_audit', 'is_sensitive',
    'status', 'created_by', 'updated_by',
])]
class Permission extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'requires_audit' => 'boolean',
            'is_sensitive' => 'boolean',
        ];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions')
            ->withPivot('granted_by')
            ->withTimestamps();
    }

    public function membershipOverrides(): HasMany
    {
        return $this->hasMany(MembershipPermissionOverride::class);
    }

    public function separationRules(): HasMany
    {
        return $this->hasMany(PermissionSeparationRule::class);
    }

    public function conflictingSeparationRules(): HasMany
    {
        return $this->hasMany(PermissionSeparationRule::class, 'conflicting_permission_id');
    }
}
