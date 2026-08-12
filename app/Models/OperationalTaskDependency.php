<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_id', 'operational_task_id', 'depends_on_task_id', 'dependency_type', 'lag_minutes', 'status'])]
class OperationalTaskDependency extends Model
{
    use HasUuids;

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(OperationalTask::class, 'operational_task_id');
    }

    public function prerequisite(): BelongsTo
    {
        return $this->belongsTo(OperationalTask::class, 'depends_on_task_id');
    }
}
