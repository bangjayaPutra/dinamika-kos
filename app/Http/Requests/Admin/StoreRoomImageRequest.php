<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoomImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'images' => ['required', 'array', 'max:6'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:10240'],
        ];
    }

    /**
     * Reject uploads that would exceed the per-room-type image limit.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $roomType = $this->route('roomType');

            if ($roomType === null) {
                return;
            }

            $incoming = count($this->file('images', []));

            if ($roomType->images()->count() + $incoming > 6) {
                $validator->errors()->add('images', 'Tipe kamar maksimal memiliki 6 gambar.');
            }
        });
    }
}
