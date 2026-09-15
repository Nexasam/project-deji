<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuoteMarketplaceBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'arrival_date' => ['required', 'date', 'after_or_equal:today'],
            'departure_date' => ['required', 'date', 'after:arrival_date'],
            'adult_count' => ['required', 'integer', 'min:1'],
            'child_count' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
