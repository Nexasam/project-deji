<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MarketplaceSearchRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:120'],
            'category' => ['nullable', Rule::in(['all', 'lekki', 'ikoyi', 'victoria-island', 'beachfront', 'family', 'business'])],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'gte:min_price'],
            'property_type' => ['nullable', Rule::in(['serviced_apartment'])],
            'beds' => ['nullable', 'integer', 'min:1', 'max:20'],
            'guests' => ['nullable', 'integer', 'min:1', 'max:50'],
            'check_in' => ['nullable', 'date', 'after_or_equal:today', 'required_with:check_out'],
            'check_out' => ['nullable', 'date', 'after:check_in', 'required_with:check_in'],
        ];
    }
}
