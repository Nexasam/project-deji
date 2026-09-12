<?php

namespace App\Services\Finance;

use App\Models\Expense;
use App\Models\FinancialTransaction;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class RecordExpense
{
    public function handle(string $businessId, ?Property $property, User $owner, string $currency, array $data): Expense
    {
        $receiptPath = isset($data['receipt']) ? $data['receipt']->store("finance/{$businessId}/receipts", 'local') : null;
        try {
            return DB::transaction(function () use ($businessId, $property, $owner, $currency, $data, $receiptPath): Expense {
            $reference = 'EXP-'.strtoupper(Str::random(10));
            $expense = Expense::query()->create([
                'business_id' => $businessId, 'property_id' => $property?->id, 'reference' => $reference,
                'category' => $data['category'], 'amount' => $data['amount'], 'currency' => $currency,
                'payee' => $data['payee'] ?? null, 'incurred_on' => $data['incurred_on'], 'paid_on' => $data['incurred_on'],
                'approval_status' => 'approved', 'approved_by' => $owner->id, 'description' => $data['description'],
                'receipt_disk' => $receiptPath ? 'local' : null, 'receipt_path' => $receiptPath, 'status' => 'active',
            ]);
            FinancialTransaction::query()->create([
                'business_id' => $businessId, 'property_id' => $property?->id, 'expense_id' => $expense->id,
                'reference' => 'TX-'.$reference, 'transaction_type' => 'expense', 'direction' => 'outflow',
                'economic_category' => $expense->category, 'source_type' => Expense::class, 'source_id' => $expense->id,
                'amount' => $expense->amount, 'currency' => $currency, 'occurred_at' => now(),
                'effective_on' => $expense->incurred_on, 'settled_at' => now(), 'description' => $expense->description,
                'transaction_status' => 'settled', 'status' => 'active', 'created_by' => $owner->id, 'updated_by' => $owner->id,
            ]);

            return $expense;
            });
        } catch (\Throwable $exception) {
            if ($receiptPath) \Illuminate\Support\Facades\Storage::disk('local')->delete($receiptPath);
            throw $exception;
        }
    }
}
