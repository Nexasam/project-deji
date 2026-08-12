<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['business_id', 'property_id', 'supplier_id', 'cost_centre_id', 'tax_category_id', 'name', 'category', 'amount', 'currency', 'frequency', 'interval', 'starts_on', 'ends_on', 'next_occurrence_on', 'recurrence_rule', 'description', 'auto_submit_for_approval', 'status', 'created_by', 'updated_by'])]
class ExpenseRecurringSchedule extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return ['amount' => 'decimal:4', 'interval' => 'integer', 'starts_on' => 'date', 'ends_on' => 'date', 'next_occurrence_on' => 'date', 'recurrence_rule' => 'array', 'auto_submit_for_approval' => 'boolean'];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'recurring_expense_schedule_id');
    }
}
