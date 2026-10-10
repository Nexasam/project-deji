<?php

namespace App\Services\Finance;

use App\Models\BookingFinancialAllocation;
use App\Models\Business;
use App\Models\BusinessSettlementAccount;
use App\Models\SettlementBatch;
use App\Models\SettlementItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class SettlementService
{
    public function available(Business $business)
    {
        return BookingFinancialAllocation::query()->where('business_id', $business->id)
            ->where('allocation_type', 'owner_balance')->where('direction', 'credit')->where('status', 'active')
            ->whereDoesntHave('settlementItem')->orderBy('recognized_on')->get();
    }

    public function create(Business $business, BusinessSettlementAccount $account, array $ids, User $creator): SettlementBatch
    {
        return DB::transaction(function () use ($business, $account, $ids, $creator): SettlementBatch {
            $business = Business::query()->lockForUpdate()->findOrFail($business->id);
            $identity = $business->verification_payload ?? [];
            if ($business->verification_status->value !== 'verified' || data_get($identity, 'nin_status') !== 'verified' || data_get($identity, 'bvn_status') !== 'verified') {
                throw ValidationException::withMessages(['business' => 'Business verification, administrator NIN and BVN must be approved before settlement.']);
            }
            if ($account->business_id !== $business->id || $account->status !== 'verified') throw ValidationException::withMessages(['account' => 'A verified settlement account is required.']);
            $ids = array_values(array_unique($ids));
            $entries = BookingFinancialAllocation::query()->whereIn('id', $ids)->where('business_id', $business->id)
                ->where('allocation_type', 'owner_balance')->where('direction', 'credit')->where('status', 'active')->lockForUpdate()->get();
            if ($entries->isEmpty() || $entries->count() !== count($ids) || SettlementItem::query()->whereIn('financial_allocation_id', $entries->pluck('id'))->exists()) {
                throw ValidationException::withMessages(['allocations' => 'Select available owner balances that have not already been settled.']);
            }
            if ($entries->pluck('currency')->unique()->count() !== 1) throw ValidationException::withMessages(['allocations' => 'A settlement batch must use one currency.']);
            $batch = SettlementBatch::query()->create(['business_id'=>$business->id,'settlement_account_id'=>$account->id,'reference'=>'SET-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),'amount'=>$entries->sum('amount'),'currency'=>$entries->first()->currency,'settlement_status'=>'draft','created_by'=>$creator->id]);
            foreach ($entries as $entry) SettlementItem::query()->create(['settlement_batch_id'=>$batch->id,'financial_allocation_id'=>$entry->id,'amount'=>$entry->amount]);
            return $batch->load('items');
        });
    }

    public function approve(SettlementBatch $batch, User $actor): SettlementBatch
    {
        return DB::transaction(function () use ($batch, $actor): SettlementBatch {
            $batch = SettlementBatch::query()->lockForUpdate()->findOrFail($batch->id);
            if ($batch->settlement_status !== 'draft') return $batch;
            if ($batch->created_by === $actor->id) throw ValidationException::withMessages(['approval' => 'The batch creator cannot approve the same settlement.']);
            $batch->update(['settlement_status'=>'approved','approved_by'=>$actor->id,'approved_at'=>now()]); return $batch->refresh();
        });
    }

    public function complete(SettlementBatch $batch, string $reference, User $actor): SettlementBatch
    {
        return DB::transaction(function () use ($batch, $reference, $actor): SettlementBatch {
            $batch = SettlementBatch::query()->lockForUpdate()->findOrFail($batch->id);
            if ($batch->settlement_status === 'completed') return $batch;
            if ($batch->settlement_status !== 'approved') throw ValidationException::withMessages(['settlement' => 'Only an approved settlement can be completed.']);
            $batch->update(['settlement_status'=>'completed','transfer_reference'=>$reference,'completed_by'=>$actor->id,'completed_at'=>now()]); return $batch->refresh();
        });
    }

    public function reverse(SettlementBatch $batch, string $reason, User $actor): SettlementBatch
    {
        return DB::transaction(function () use ($batch, $reason, $actor): SettlementBatch {
            $batch = SettlementBatch::query()->lockForUpdate()->findOrFail($batch->id);
            if ($batch->settlement_status === 'reversed') return $batch;
            if ($batch->settlement_status !== 'completed') throw ValidationException::withMessages(['settlement' => 'Only a completed settlement can be reversed.']);
            $batch->update(['settlement_status'=>'reversed','reversal_reason'=>$reason,'reversed_by'=>$actor->id,'reversed_at'=>now()]); return $batch->refresh();
        });
    }
}
