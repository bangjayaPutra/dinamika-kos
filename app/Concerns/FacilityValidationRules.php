<?php

namespace App\Concerns;

use Illuminate\Contracts\Validation\ValidationRule;

trait FacilityValidationRules
{
    /**
     * Get the validation rules used to validate facilities.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function facilityRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'scope' => ['required', 'string', 'in:kamar,umum'],
        ];
    }
}
