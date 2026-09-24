<?php

namespace App\Concerns;

use Illuminate\Contracts\Validation\ValidationRule;

trait RoomTypeValidationRules
{
    /**
     * Get the validation rules used to validate room types.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function roomTypeRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price_monthly' => ['required', 'integer', 'min:0'],
            'size_label' => ['nullable', 'string', 'max:50'],
            'capacity' => ['required', 'integer', 'min:1', 'max:10'],
            'stock_total' => ['required', 'integer', 'min:0'],
            'is_available' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'facilities' => ['sometimes', 'array'],
            'facilities.*' => ['integer', 'exists:facilities,id'],
        ];
    }
}
