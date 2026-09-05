<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'first_name'    => ['required', 'string', 'max:255'],
            'last_name'     => ['required', 'string', 'max:255'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'country'       => ['nullable', 'string', Rule::in(config('countries.countries'))],
            'address'       => ['nullable', 'string', 'max:255'],
            'phone'         => ['nullable', 'string', 'regex:/^03\d{2}-\d{7}$/'],
            'gender'        => ['nullable', 'string', 'in:male,female,non-binary,other'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Your phone number is not valid. It must be in the format 0300-1234567.',
        ];
    }
}