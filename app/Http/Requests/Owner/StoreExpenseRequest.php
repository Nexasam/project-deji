<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool { return $this->user() !== null; }

    public function rules(): array
    {
        return [
            'property_id' => ['nullable', 'uuid'], 'category' => ['required', 'in:cleaning,maintenance,utilities,supplies,staff,commission,other'],
            'amount' => ['required', 'numeric', 'gt:0', 'decimal:0,4'], 'incurred_on' => ['required', 'date'],
            'description' => ['required', 'string', 'max:2000'], 'payee' => ['nullable', 'string', 'max:255'],
            'receipt' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
        ];
    }
}
