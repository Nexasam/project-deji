<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMarketplaceBookingRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'payment_provider' => $this->input('payment_provider', 'paystack'),
            'payment_option' => $this->input('payment_option', 'full'),
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['arrival_date' => ['required', 'date', 'after_or_equal:today'], 'departure_date' => ['required', 'date', 'after:arrival_date'], 'adult_count' => ['required', 'integer', 'min:1'], 'child_count' => ['nullable', 'integer', 'min:0'], 'guest_phone' => ['required', 'string', 'max:32'], 'special_requests' => ['nullable', 'string', 'max:1000'], 'quoted_total' => ['required', 'numeric', 'min:0'], 'idempotency_key' => ['required', 'string', 'max:100'], 'payment_provider' => ['required', 'in:paystack,flutterwave'], 'payment_option' => ['required', 'in:full,installment'], 'deposit_amount' => ['nullable', 'numeric', 'min:0']];
    }
}
