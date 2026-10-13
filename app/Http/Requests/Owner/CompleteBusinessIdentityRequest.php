<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class CompleteBusinessIdentityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'nin' => ['required', 'string', 'regex:/^\d{11}$/'],
            'identity_consent' => ['required', 'accepted'],
        ];
    }
}
