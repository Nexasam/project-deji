<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['ai_knowledge_source_id', 'chunk_index', 'content', 'content_hash', 'vector_reference', 'token_count', 'metadata', 'status'])]
class AiKnowledgeChunk extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(AiKnowledgeSource::class, 'ai_knowledge_source_id');
    }
}
