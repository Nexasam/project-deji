<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable(['business_id', 'property_id', 'forecast_type', 'horizon_days', 'forecast_starts_on', 'forecast_ends_on', 'currency', 'forecast_values', 'assumptions', 'supporting_metrics', 'model_provider', 'model_name', 'model_version', 'calculation_version', 'generated_at', 'status', 'created_by', 'updated_by'])]
class FinancialForecastSnapshot extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::deleting(function (): never {
            throw new LogicException('Financial forecast history cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return ['horizon_days' => 'integer', 'forecast_starts_on' => 'date', 'forecast_ends_on' => 'date', 'forecast_values' => 'array', 'assumptions' => 'array', 'supporting_metrics' => 'array', 'generated_at' => 'datetime'];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
