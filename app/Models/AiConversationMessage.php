<?php

namespace App\Models;

use App\Enums\AiMessageRole;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['ai_conversation_id', 'parent_message_id', 'sequence', 'role', 'content', 'tool_calls', 'citations', 'model_provider', 'model_name', 'input_tokens', 'output_tokens', 'duration_ms', 'message_status', 'failure_reason', 'metadata', 'status'])]
class AiConversationMessage extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['role' => AiMessageRole::class, 'tool_calls' => 'array', 'citations' => 'array', 'metadata' => 'array'];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AiConversation::class, 'ai_conversation_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_message_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_message_id');
    }
}
