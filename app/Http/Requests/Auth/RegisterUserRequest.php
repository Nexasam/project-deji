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
            'account_type' => ['required', 'in:individual,business'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)],
            'phone_number' => ['nullable', 'string', 'max:32', 'regex:/^\+?[0-9\s()-]{7,32}$/'],
            'business_name' => ['nullable', 'required_if:account_type,business', 'string', 'max:255'],
            'nin' => ['nullable', 'string', 'regex:/^\d{11}$/'],
            'bvn' => ['nullable', 'string', 'regex:/^\d{11}$/'],
            'password' => app(PasswordPolicyRules::class)->rules(),
            'terms' => ['accepted'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => mb_strtolower(trim((string) $this->email)),
            'phone_number' => $this->phone_number ? preg_replace('/[^0-9+]/', '', (string) $this->phone_number) : null,
            'account_type' => $this->account_type ?: 'individual',
            'nin' => $this->nin ? preg_replace('/\D/', '', (string) $this->nin) : null,
            'bvn' => $this->bvn ? preg_replace('/\D/', '', (string) $this->bvn) : null,
        ]);
    }
}
