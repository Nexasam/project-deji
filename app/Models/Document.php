<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'business_id', 'owner_type', 'owner_id', 'title', 'category', 'document_number',
    'issuer', 'description', 'searchable_text', 'issued_on', 'effective_on', 'expires_on',
    'reminder_days_before_expiry', 'last_expiry_reminder_at', 'confidentiality',
    'verification_status', 'verified_by', 'verified_at', 'current_version_number', 'status',
])]
class Document extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'issued_on' => 'date',
            'effective_on' => 'date',
            'expires_on' => 'date',
            'reminder_days_before_expiry' => 'integer',
            'last_expiry_reminder_at' => 'datetime',
            'verified_at' => 'datetime',
            'current_version_number' => 'integer',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(DocumentPermission::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function aiKnowledgeSources(): HasMany
    {
        return $this->hasMany(AiKnowledgeSource::class);
    }
}
