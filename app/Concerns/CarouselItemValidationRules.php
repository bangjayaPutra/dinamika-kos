<?php

namespace App\Concerns;

use Illuminate\Contracts\Validation\ValidationRule;

trait CarouselItemValidationRules
{
    /**
     * Get the validation rules used to validate carousel items.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function carouselItemRules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:10240'],
            'link_url' => ['nullable', 'url', 'max:2000'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
