<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'business_id', 'business_membership_id', 'active_user_role_id', 'switched_at',
    'status', 'created_by', 'updated_by',
])]
class UserBusinessContext extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['switched_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(BusinessMembership::class, 'business_membership_id');
    }

    public function activeRoleAssignment(): BelongsTo
    {
        return $this->belongsTo(UserRole::class, 'active_user_role_id');
    }
}
