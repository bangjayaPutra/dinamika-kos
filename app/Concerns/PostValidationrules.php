<?php

namespace App\Concerns;

use Illuminate\Contracts\Validation\ValidationRule;

trait PostValidationRules
{
    /**
     * Get the validation rules used to validate posts.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function postRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'body' => ['required', 'string'],
            'cover_path' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:10240'],
            'is_published' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
