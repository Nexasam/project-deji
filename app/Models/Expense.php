<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

#[Fillable(['business_id', 'property_id', 'booking_id', 'operational_task_id', 'maintenance_issue_id', 'supplier_id', 'employee_id', 'cost_centre_id', 'tax_category_id', 'recurring_expense_schedule_id', 'reference', 'category', 'amount', 'subtotal_amount', 'tax_amount', 'currency', 'payee', 'provider', 'provider_reference', 'incurred_on', 'due_on', 'paid_on', 'approval_status', 'submitted_by', 'submitted_at', 'approved_by', 'approved_at', 'rejection_reason', 'verified_by', 'description', 'receipt_disk', 'receipt_path', 'status'])]
class Expense extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::deleting(function (): never {
            throw new LogicException('Expenses are auditable records and cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return ['amount' => 'decimal:4', 'subtotal_amount' => 'decimal:4', 'tax_amount' => 'decimal:4', 'incurred_on' => 'date', 'due_on' => 'date', 'paid_on' => 'date', 'submitted_at' => 'datetime', 'approved_at' => 'datetime'];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function costCentre(): BelongsTo
    {
        return $this->belongsTo(CostCentre::class);
    }

    public function taxCategory(): BelongsTo
    {
        return $this->belongsTo(TaxCategory::class);
    }

    public function recurringSchedule(): BelongsTo
    {
        return $this->belongsTo(ExpenseRecurringSchedule::class, 'recurring_expense_schedule_id');
    }

    public function approvalEvents(): HasMany
    {
        return $this->hasMany(ExpenseApprovalEvent::class);
    }

    public function financialTransactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class);
    }
}
