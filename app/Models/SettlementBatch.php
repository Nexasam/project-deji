<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['business_id','settlement_account_id','reference','amount','currency','settlement_status','created_by','approved_by','completed_by','reversed_by','transfer_reference','reversal_reason','approved_at','completed_at','reversed_at'])]
class SettlementBatch extends Model
{
    use HasUuids;
    protected $table = 'platform_settlement_batches';
    protected function casts(): array { return ['amount'=>'decimal:4','approved_at'=>'datetime','completed_at'=>'datetime','reversed_at'=>'datetime']; }
    public function business(): BelongsTo { return $this->belongsTo(Business::class); }
    public function account(): BelongsTo { return $this->belongsTo(BusinessSettlementAccount::class, 'settlement_account_id'); }
    public function items(): HasMany { return $this->hasMany(SettlementItem::class); }
}
