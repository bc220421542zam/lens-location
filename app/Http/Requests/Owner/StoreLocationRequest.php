<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:255'],
            'category'       => ['required', 'string', 'max:100'],
            'price_per_hour' => ['required', 'numeric', 'min:0'],
            'description'    => ['required', 'string'],
            'address'        => ['required', 'string', 'max:255'],
            'city'           => ['required', 'string', 'max:100'],
            'image'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }
}
