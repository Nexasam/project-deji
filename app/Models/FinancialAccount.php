<?php

namespace App\Models;

use App\Enums\FinancialAccountType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['business_id', 'code', 'name', 'account_type', 'provider', 'external_account_reference', 'currency', 'opening_balance', 'opening_balance_date', 'is_default', 'status', 'created_by', 'updated_by'])]
class FinancialAccount extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return ['account_type' => FinancialAccountType::class, 'opening_balance' => 'decimal:4', 'opening_balance_date' => 'date', 'is_default' => 'boolean'];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class);
    }

    public function reconciliations(): HasMany
    {
        return $this->hasMany(FinancialReconciliation::class);
    }
}
