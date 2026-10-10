<?php

namespace App\Jobs;

use App\Models\RefundRequest;
use App\Services\Payments\ProviderRefundService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class InitiateProviderRefund implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [60, 300, 900];

    public function __construct(public readonly string $refundRequestId) {}

    public function handle(ProviderRefundService $refunds): void
    {
        $refunds->initiate(RefundRequest::query()->findOrFail($this->refundRequestId));
    }
}
