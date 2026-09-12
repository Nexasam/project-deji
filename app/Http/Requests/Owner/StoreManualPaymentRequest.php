<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StoreManualPaymentRequest extends FormRequest
{
    public function authorize(): bool { return $this->user() !== null; }

    public function rules(): array
    {
        return [
            'booking_id' => ['required', 'uuid'], 'reference' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'gt:0', 'decimal:0,4'],
            'method' => ['required', 'in:bank_transfer,cash,card,wallet'],
            'transaction_at' => ['required', 'date'], 'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
