<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

#[Fillable(['business_id', 'base_currency', 'quote_currency', 'rate', 'provider', 'effective_at', 'expires_at', 'provider_metadata', 'status', 'created_by', 'updated_by'])]
class CurrencyExchangeRate extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::deleting(function (): never {
            throw new LogicException('Exchange-rate evidence cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return ['rate' => 'decimal:10', 'effective_at' => 'datetime', 'expires_at' => 'datetime', 'provider_metadata' => 'array'];
    }

    public function conversions(): HasMany
    {
        return $this->hasMany(CurrencyConversion::class, 'exchange_rate_id');
    }
}
