<?php

namespace App\Services\Payments;

use App\Enums\RefundRequestStatus;
use App\Models\RefundRequest;
use App\Models\BookingFinancialAllocation;
use App\Models\Payment;
use App\Services\Platform\PlatformSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

final class ProviderRefundService
{
    public function __construct(private readonly PlatformSettings $settings) {}

    public function initiate(RefundRequest $request): void
    {
        $request->loadMissing(['originalPayment', 'refundPayment']);
        if (! in_array($request->refund_status, [RefundRequestStatus::Approved, RefundRequestStatus::Failed], true)) {
            return;
        }

        $payment = $request->refundPayment;
        $original = $request->originalPayment;
        if (! $original?->provider || blank($original->provider_reference)) {
            $request->forceFill([
                'refund_status' => RefundRequestStatus::Failed,
                'processed_at' => now(),
                'failure_reason' => 'The original charge has no supported provider reference. Manual finance review is required.',
            ])->save();
            $payment?->forceFill([
                'status' => 'failed',
                'notes' => 'Automatic refund unavailable; manual finance review is required.',
            ])->save();

            return;
        }

        $request->forceFill(['refund_status' => RefundRequestStatus::Processing, 'processed_at' => now(), 'failure_reason' => null])->save();

        try {
            $response = match ($original->provider->value) {
                'paystack' => Http::acceptJson()->asJson()->withToken($this->secret('paystack'))->timeout(20)->retry(2, 250)
                    ->post('https://api.paystack.co/refund', [
                        'transaction' => $original->provider_reference,
                        'amount' => (int) round(((float) $request->approved_amount) * 100),
                        'currency' => $request->currency,
                        'merchant_note' => $request->reason ?: 'Booking cancellation',
                    ])->throw()->json(),
                'flutterwave' => Http::acceptJson()->asJson()->withToken($this->secret('flutterwave'))->timeout(20)->retry(2, 250)
                    ->post('https://api.flutterwave.com/v3/transactions/'.rawurlencode((string) data_get($original->provider_metadata, 'provider_transaction_id')).'/refund', [
                        'amount' => (float) $request->approved_amount,
                        'comments' => $request->reason ?: 'Booking cancellation',
                    ])->throw()->json(),
                default => throw new RuntimeException('This payment provider does not support automated refunds.'),
            };

            $providerRefundId = data_get($response, 'data.id') ?? data_get($response, 'data.refund_reference');
            $accepted = data_get($response, 'status') === true || data_get($response, 'status') === 'success';
            if (! $accepted) {
                throw new RuntimeException('The provider did not accept the refund request.');
            }

            $payment->forceFill([
                'status' => 'processing',
                'provider_reference' => filled($providerRefundId) ? (string) $providerRefundId : $payment->reference,
                'provider_metadata' => array_merge($payment->provider_metadata ?? [], ['initiation' => $response]),
            ])->save();
        } catch (Throwable $exception) {
            $request->forceFill(['refund_status' => RefundRequestStatus::Failed, 'failure_reason' => mb_substr($exception->getMessage(), 0, 2000)])->save();
            $payment->forceFill(['status' => 'failed', 'notes' => 'Provider refund initiation failed.'])->save();
            throw $exception;
        }
    }

    /** @param array<string, mixed> $data */
    public function handlePaystackWebhook(string $event, array $data): void
    {
        if (! str_starts_with($event, 'refund.')) {
            return;
        }

        $originalReference = (string) data_get($data, 'transaction_reference', '');
        if ($originalReference === '') {
            return;
        }

        DB::transaction(function () use ($event, $data, $originalReference): void {
            $original = Payment::query()->where('provider', 'paystack')->where('provider_reference', $originalReference)->first();
            if (! $original) return;
            $request = RefundRequest::query()->with('refundPayment')->where('original_payment_id', $original->id)->lockForUpdate()->latest('requested_at')->first();
            if (! $request) return;

            if ($event === 'refund.failed' || $event === 'refund.needs-attention') {
                $request->forceFill(['refund_status' => RefundRequestStatus::Failed, 'failure_reason' => (string) data_get($data, 'reason', $event)])->save();
                $request->refundPayment?->forceFill(['status' => 'failed', 'provider_metadata' => array_merge($request->refundPayment->provider_metadata ?? [], ['webhook' => $data])])->save();
                return;
            }

            if ($event !== 'refund.processed') {
                $request->forceFill(['refund_status' => RefundRequestStatus::Processing])->save();
                $request->refundPayment?->forceFill(['status' => 'processing', 'provider_metadata' => array_merge($request->refundPayment->provider_metadata ?? [], ['webhook' => $data])])->save();
                return;
            }

            $amount = ((float) data_get($data, 'amount', 0)) / 100;
            if (abs($amount - (float) $request->approved_amount) > 0.0001 || strtoupper((string) data_get($data, 'currency')) !== strtoupper($request->currency)) {
                throw new RuntimeException('Refund webhook amount or currency does not match the approved refund.');
            }

            $refund = $request->refundPayment;
            if ($refund->status->value !== 'completed') {
                $refund->forceFill(['status' => 'completed', 'verified_at' => now(), 'transaction_at' => now(), 'provider_metadata' => array_merge($refund->provider_metadata ?? [], ['webhook' => $data])])->save();
                $ratio = (float) $original->amount > 0 ? min(1, (float) $refund->amount / (float) $original->amount) : 0;
                foreach ($original->financialAllocations as $allocation) {
                    $reversalAmount = round((float) $allocation->amount * $ratio, 2);
                    if ($reversalAmount <= 0) continue;
                    BookingFinancialAllocation::query()->create(['business_id' => $refund->business_id, 'booking_id' => $refund->booking_id, 'payment_id' => $refund->id, 'allocation_type' => $allocation->allocation_type.'_reversal', 'direction' => 'debit', 'amount' => $reversalAmount, 'currency' => $allocation->currency, 'recognized_on' => now()->toDateString(), 'description' => 'Refund reversal for '.$allocation->description, 'status' => 'active']);
                }
            }
            $request->forceFill(['refund_status' => RefundRequestStatus::Completed, 'completed_at' => now(), 'failure_reason' => null])->save();
            $booking = $request->booking()->firstOrFail();
            $paid = (float) $booking->payments()->where('status', 'completed')->where('purpose', '!=', 'refund')->sum('amount');
            $refunded = (float) $booking->payments()->where('status', 'completed')->where('purpose', 'refund')->sum('amount');
            $booking->update(['payment_status' => $refunded + 0.0001 >= $paid ? 'refunded' : 'partially_refunded']);
        });
    }

    private function secret(string $provider): string
    {
        $secret = $this->settings->paymentCredential($provider, 'secret_key');
        if ($secret === '') {
            throw new RuntimeException(ucfirst($provider).' secret key is not configured.');
        }

        return $secret;
    }
}
