<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_id', 'step_key', 'sort_order', 'state', 'completed_at', 'completed_by', 'metadata', 'status'])]
class BusinessOnboardingStep extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['sort_order' => 'integer', 'completed_at' => 'datetime', 'metadata' => 'array'];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
