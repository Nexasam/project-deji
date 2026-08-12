<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable(['business_id', 'financial_report_definition_id', 'financial_report_schedule_id', 'run_status', 'parameters', 'period_starts_on', 'period_ends_on', 'summary', 'storage_disk', 'storage_path', 'format', 'checksum', 'started_at', 'completed_at', 'delivered_at', 'failure_reason', 'status', 'created_by', 'updated_by'])]
class FinancialReportRun extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::deleting(function (): never {
            throw new LogicException('Generated financial report history cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return ['parameters' => 'array', 'period_starts_on' => 'date', 'period_ends_on' => 'date', 'summary' => 'array', 'started_at' => 'datetime', 'completed_at' => 'datetime', 'delivered_at' => 'datetime'];
    }

    public function definition(): BelongsTo
    {
        return $this->belongsTo(FinancialReportDefinition::class, 'financial_report_definition_id');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(FinancialReportSchedule::class, 'financial_report_schedule_id');
    }
}
