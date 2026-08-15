<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePropertyAmenitiesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'amenities' => ['sometimes', 'array'],
            'amenities.*' => [
                'uuid',
                'distinct',
                Rule::exists('amenities', 'id')->where('status', 'active'),
            ],
        ];
    }

    /** @return list<string> */
    public function amenityIds(): array
    {
        return array_values($this->validated('amenities', []));
    }
}
