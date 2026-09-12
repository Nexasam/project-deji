<?php

namespace App\Services\Finance;

use App\Models\Business;
use Illuminate\Support\Collection;

final class OwnerFinanceWorkspace
{
    public function build(Business $business, array $filters): array
    {
        $payments = $business->payments()->with('booking.property')->whereIn('status', ['completed', 'pending', 'processing'])
            ->when($filters['property'] ?? null, fn ($q, $id) => $q->whereHas('booking', fn ($b) => $b->where('property_id', $id)))
            ->when($filters['from'] ?? null, fn ($q, $date) => $q->whereDate('transaction_at', '>=', $date))
            ->when($filters['to'] ?? null, fn ($q, $date) => $q->whereDate('transaction_at', '<=', $date))->get();
        $expenses = $business->expenses()->with('property')->where('status', 'active')
            ->when($filters['property'] ?? null, fn ($q, $id) => $q->where('property_id', $id))
            ->when($filters['from'] ?? null, fn ($q, $date) => $q->whereDate('incurred_on', '>=', $date))
            ->when($filters['to'] ?? null, fn ($q, $date) => $q->whereDate('incurred_on', '<=', $date))->get();

        $completed = $payments->filter(fn ($payment) => $payment->status->value === 'completed');
        $gross = (float) $completed->reject(fn ($p) => $p->purpose->value === 'refund')->sum('amount');
        $refunds = (float) $completed->filter(fn ($p) => $p->purpose->value === 'refund')->sum('amount');
        $expenseTotal = (float) $expenses->sum('amount');
        $revenue = $gross - $refunds;
        $pending = (float) $payments->reject(fn ($p) => $p->status->value === 'completed')->sum('amount');
        $transactions = $this->transactions($payments, $expenses, $filters['type'] ?? null, $filters['q'] ?? null);

        return [
            'currency' => $business->currency,
            'totals' => compact('revenue', 'refunds', 'expenseTotal', 'pending') + ['net' => $revenue - $expenseTotal],
            'transactions' => $transactions,
            'revenueByProperty' => $completed->groupBy(fn ($p) => $p->booking?->property?->name ?? 'Unassigned')->map(function ($rows, $name): array {
                $amount = $rows->sum(fn ($payment) => $payment->purpose->value === 'refund' ? -((float) $payment->amount) : (float) $payment->amount);

                return ['name' => $name, 'amount' => $amount];
            })->filter(fn ($row) => $row['amount'] != 0)->sortByDesc('amount')->values(),
            'expensesByCategory' => $expenses->groupBy('category')->map(fn ($rows, $name) => ['name' => str($name)->replace('_', ' ')->title()->toString(), 'amount' => (float) $rows->sum('amount')])->sortByDesc('amount')->values(),
        ];
    }

    private function transactions(Collection $payments, Collection $expenses, ?string $type, ?string $search): Collection
    {
        $rows = $payments->map(fn ($p) => ['date' => $p->transaction_at, 'property' => $p->booking?->property?->name ?? 'Unassigned', 'type' => $p->purpose->value === 'refund' ? 'refund' : 'payment', 'description' => $p->purpose->value === 'refund' ? 'Refund '.$p->reference : 'Booking '.$p->booking?->reference, 'reference' => $p->reference, 'status' => $p->status->value, 'amount' => (float) $p->amount, 'positive' => $p->purpose->value !== 'refund'])
            ->concat($expenses->map(fn ($e) => ['date' => $e->incurred_on, 'property' => $e->property?->name ?? 'General business', 'type' => 'expense', 'description' => $e->description, 'reference' => $e->reference, 'status' => $e->approval_status, 'amount' => (float) $e->amount, 'positive' => false]));
        if ($type) $rows = $rows->where('type', $type);
        if ($search) $rows = $rows->filter(fn ($row) => str_contains(strtolower($row['description'].' '.$row['reference'].' '.$row['property']), strtolower($search)));

        return $rows->sortByDesc('date')->values();
    }
}
