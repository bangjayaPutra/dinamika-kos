<?php

namespace App\Concerns;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

trait UserValidationRules
{
    /**
     * Get the validation rules used to validate users.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function userRules(?int $ignoreId = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($ignoreId)],
            'role' => ['required', 'string', Rule::in(['admin', 'user'])],
        ];
    }

    /**
     * Get the validation rules used to validate a new user password.
     *
     * @return array<int, Password|ValidationRule|array<mixed>|string>
     */
    protected function newPasswordRules(): array
    {
        return ['required', 'string', Password::default(), 'confirmed'];
    }

    /**
     * Get the validation rules used to validate an optional password change.
     *
     * @return array<int, Password|ValidationRule|array<mixed>|string>
     */
    protected function optionalPasswordRules(): array
    {
        return ['nullable', 'string', Password::default(), 'confirmed'];
    }
}
