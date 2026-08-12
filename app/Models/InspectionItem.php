<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_id', 'inspection_id', 'corrective_task_id', 'category', 'item_name', 'expected_value', 'observed_value', 'result', 'severity', 'notes', 'evidence', 'sort_order', 'status'])]
class InspectionItem extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['evidence' => 'array', 'sort_order' => 'integer'];
    }

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }
}
