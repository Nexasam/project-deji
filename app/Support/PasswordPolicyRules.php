<?php

namespace App\Support;

use App\Models\PasswordPolicy;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;
use Throwable;

final class PasswordPolicyRules
{
    public function rules(): array
    {
        $settings = ['minimum_length' => 12, 'requires_uppercase' => true, 'requires_lowercase' => true, 'requires_number' => true, 'requires_symbol' => false];

        try {
            if (Schema::hasTable('password_policies')) {
                $policy = PasswordPolicy::query()->where('system_key', 'platform-default')->where('status', 'active')->first();
                if ($policy) {
                    $settings = $policy->only(array_keys($settings));
                }
            }
        } catch (Throwable) {
            // Secure defaults keep validation available before the first migration.
        }

        $rule = Password::min((int) $settings['minimum_length']);
        if ($settings['requires_uppercase']) {
            $rule->mixedCase();
        } elseif ($settings['requires_lowercase']) {
            $rule->letters();
        }
        if ($settings['requires_number']) {
            $rule->numbers();
        }
        if ($settings['requires_symbol']) {
            $rule->symbols();
        }

        return ['required', 'confirmed', $rule];
    }
}
