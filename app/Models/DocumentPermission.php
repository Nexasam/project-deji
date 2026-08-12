<?php

namespace App\Models;

use App\Enums\DocumentAccessLevel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable([
    'business_id', 'document_id', 'user_id', 'user_role_id', 'access_level',
    'expires_at', 'status',
])]
class DocumentPermission extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::saving(function (DocumentPermission $permission): void {
            if (($permission->user_id === null) === ($permission->user_role_id === null)) {
                throw new LogicException('A document permission must target one user or one user role.');
            }
        });
    }

    protected function casts(): array
    {
        return ['access_level' => DocumentAccessLevel::class, 'expires_at' => 'datetime'];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userRole(): BelongsTo
    {
        return $this->belongsTo(UserRole::class);
    }
}
