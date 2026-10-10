<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['settlement_batch_id','financial_allocation_id','amount'])]
class SettlementItem extends Model
{
    use HasUuids;
    protected $table = 'platform_settlement_items';
    protected function casts(): array { return ['amount'=>'decimal:4']; }
    public function batch(): BelongsTo { return $this->belongsTo(SettlementBatch::class, 'settlement_batch_id'); }
    public function allocation(): BelongsTo { return $this->belongsTo(BookingFinancialAllocation::class, 'financial_allocation_id'); }
}
