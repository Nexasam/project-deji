<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'property_type' => ['required', Rule::in(['apartment', 'short_let'])],
            'address_line' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:120'],
            'state' => ['required', 'string', 'max:120'],
            'country_code' => ['required', 'string', 'size:2'],
            'capacity' => ['required', 'integer', 'min:1', 'max:1000'],
            'bedrooms' => ['required', 'integer', 'min:0', 'max:500'],
            'bathrooms' => ['required', 'numeric', 'min:0', 'max:500'],
            'description' => ['nullable', 'string', 'max:5000'],
            'default_nightly_price' => ['nullable', 'numeric', 'min:0', 'max:999999999999999'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->name),
            'country_code' => strtoupper(trim((string) $this->country_code)),
        ]);
    }

    public function propertyAttributes(): array
    {
        $data = $this->safe()->except(['address_line', 'city', 'state', 'country_code']);
        $data['address'] = [
            'line_1' => $this->string('address_line')->toString(),
            'city' => $this->string('city')->toString(),
            'state' => $this->string('state')->toString(),
            'country_code' => $this->string('country_code')->toString(),
        ];

        return $data;
    }
}
