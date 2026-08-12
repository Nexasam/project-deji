<?php

namespace App\Models;

use App\Enums\BusinessMembershipStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'business_id',
    'user_id',
    'job_title',
    'status',
    'invited_at',
    'joined_at',
    'ended_at',
])]
class BusinessMembership extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'status' => BusinessMembershipStatus::class,
            'invited_at' => 'datetime',
            'joined_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }
}
