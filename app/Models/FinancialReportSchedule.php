<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['business_id', 'financial_report_definition_id', 'frequency', 'schedule_rule', 'formats', 'delivery_channels', 'recipients', 'next_run_at', 'last_run_at', 'status', 'created_by', 'updated_by'])]
class FinancialReportSchedule extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return ['schedule_rule' => 'array', 'formats' => 'array', 'delivery_channels' => 'array', 'recipients' => 'array', 'next_run_at' => 'datetime', 'last_run_at' => 'datetime'];
    }

    public function definition(): BelongsTo
    {
        return $this->belongsTo(FinancialReportDefinition::class, 'financial_report_definition_id');
    }

    public function runs(): HasMany
    {
        return $this->hasMany(FinancialReportRun::class);
    }
}
