<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Support\PasswordPolicyRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)],
            'phone_number' => ['nullable', 'string', 'max:32', 'regex:/^\+?[0-9\s()-]{7,32}$/'],
            'password' => app(PasswordPolicyRules::class)->rules(),
            'terms' => ['accepted'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => mb_strtolower(trim((string) $this->email)),
            'phone_number' => $this->phone_number ? preg_replace('/[^0-9+]/', '', (string) $this->phone_number) : null,
        ]);
    }
}
