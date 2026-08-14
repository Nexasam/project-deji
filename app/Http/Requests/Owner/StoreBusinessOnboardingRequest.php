<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StoreBusinessOnboardingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:100'],
            'country_code' => ['required', 'string', 'size:2'],
            'business_type' => ['required', 'string', 'max:80'],
            'timezone' => ['required', 'timezone'],
            'currency' => ['required', 'string', 'size:3'],
            'primary_contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:32'],
            'address_line' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['country_code' => strtoupper((string) $this->country_code), 'currency' => strtoupper((string) $this->currency)]);
    }

    public function businessAttributes(): array
    {
        $data = $this->safe()->except('address_line');
        $data['address'] = $this->filled('address_line') ? ['line' => $this->string('address_line')->toString()] : null;

        return $data;
    }
}
