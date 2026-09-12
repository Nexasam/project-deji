<?php

namespace App\Services\Finance;

use App\Models\Booking;
use App\Models\FinancialTransaction;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class RecordManualPayment
{
    public function handle(Booking $booking, User $owner, array $data): Payment
    {
        return DB::transaction(function () use ($booking, $owner, $data): Payment {
            $booking = Booking::query()->lockForUpdate()->findOrFail($booking->id);
            if (Payment::query()->where('business_id', $booking->business_id)->where('reference', $data['reference'])->exists()) {
                throw ValidationException::withMessages(['reference' => 'This payment reference has already been recorded.']);
            }
            $paidBefore = (float) $booking->payments()->where('status', 'completed')->where('purpose', '!=', 'refund')->sum('amount')
                - (float) $booking->payments()->where('status', 'completed')->where('purpose', 'refund')->sum('amount');
            $outstanding = max(0, (float) $booking->total_amount - $paidBefore);
            if ((float) $data['amount'] > $outstanding) {
                throw ValidationException::withMessages(['amount' => 'The payment cannot exceed the outstanding booking balance.']);
            }

            $payment = Payment::query()->create([
                'business_id' => $booking->business_id, 'booking_id' => $booking->id,
                'reference' => $data['reference'], 'purpose' => 'balance', 'amount' => $data['amount'],
                'currency' => $booking->currency, 'method' => $data['method'], 'provider' => null,
                'status' => 'completed', 'transaction_at' => $data['transaction_at'],
                'verified_by' => $owner->id, 'verified_at' => now(), 'notes' => $data['notes'] ?? null,
                'provider_metadata' => ['mode' => 'manual', 'gateway_connection' => 'pending'], 'created_by' => $owner->id,
            ]);
            FinancialTransaction::query()->create([
                'business_id' => $booking->business_id, 'property_id' => $booking->property_id,
                'booking_id' => $booking->id, 'payment_id' => $payment->id, 'reference' => 'TX-'.$payment->reference,
                'transaction_type' => 'guest_payment', 'direction' => 'inflow', 'economic_category' => 'booking_revenue',
                'source_type' => Payment::class, 'source_id' => $payment->id, 'amount' => $payment->amount,
                'currency' => $payment->currency, 'occurred_at' => $payment->transaction_at,
                'effective_on' => $payment->transaction_at->toDateString(), 'settled_at' => $payment->transaction_at,
                'description' => 'Manual payment for '.$booking->reference, 'transaction_status' => 'settled',
                'status' => 'active', 'created_by' => $owner->id, 'updated_by' => $owner->id,
            ]);

            $paid = (float) $booking->payments()->where('status', 'completed')->where('purpose', '!=', 'refund')->sum('amount')
                - (float) $booking->payments()->where('status', 'completed')->where('purpose', 'refund')->sum('amount');
            $booking->update(['payment_status' => $paid >= (float) $booking->total_amount ? 'paid' : 'partially_paid']);

            return $payment;
        });
    }
}
