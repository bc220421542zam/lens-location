<?php

namespace App\Http\Requests\Auth;

use App\Enums\Role;
use App\Support\PasswordRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => preg_replace('/[\s\-\(\)\.]/', '', $this->phone),
        ]);
    }

    public function rules(): array
    {
        return [
            'role'       => ['required', new Enum(Role::class)],
            'first_name' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[a-zA-Z\s\-]+$/'],
            'last_name'  => ['required', 'string', 'min:2', 'max:255', 'regex:/^[a-zA-Z\s\-]+$/'],
            'email'      => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'phone'      => ['required', 'string', 'max:20', 'regex:/^\+?[0-9]{7,20}$/'],
            'password'   => PasswordRules::strong(),
        ];
    }

    public function messages(): array
    {
        return [
            'email.email'      => 'Please enter a valid email address with a working domain.',
            'first_name.regex' => 'First name may only contain letters, spaces, and hyphens.',
            'last_name.regex'  => 'Last name may only contain letters, spaces, and hyphens.',
            'phone.regex'      => 'Please enter a valid phone number.',
            ...PasswordRules::messages(),
        ];
    }
}
