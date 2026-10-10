<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_id','bank_name','account_name','account_number','account_number_last4','status','verified_by','verified_at'])]
class BusinessSettlementAccount extends Model
{
    use HasUuids;
    protected $table = 'platform_settlement_accounts';
    protected $hidden = ['account_number'];
    protected function casts(): array { return ['account_number' => 'encrypted', 'verified_at' => 'datetime']; }
    public function business(): BelongsTo { return $this->belongsTo(Business::class); }
    public function getMaskedNumberAttribute(): string { return '•••• •••• '.$this->account_number_last4; }
}
