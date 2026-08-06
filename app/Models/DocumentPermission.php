<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'business_id', 'document_id', 'grantee_type', 'grantee_id',
    'permission', 'status', 'expires_at', 'created_by', 'updated_by',
])]
class DocumentPermission extends Model
{
    use HasFactory, HasUuids;

    protected function casts(): array
    {
        return ['expires_at' => 'datetime'];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function grantee(): MorphTo
    {
        return $this->morphTo();
    }
}
