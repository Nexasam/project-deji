<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['business_id', 'document_id', 'source_type', 'title', 'source_uri', 'access_scope', 'content_hash', 'indexing_status', 'index_version', 'last_indexed_at', 'indexing_error', 'metadata', 'status'])]
class AiKnowledgeSource extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return ['last_indexed_at' => 'datetime', 'metadata' => 'array'];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function chunks(): HasMany
    {
        return $this->hasMany(AiKnowledgeChunk::class);
    }
}
