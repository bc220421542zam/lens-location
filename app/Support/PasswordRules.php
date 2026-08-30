<?php

namespace App\Support;

class PasswordRules
{
    /**
     * Validation rules for a strong password: min 8 chars with at least
     * one lowercase letter, one uppercase letter, one digit, and one symbol.
     */
    public static function strong(): array
    {
        return [
            'required',
            'string',
            'min:8',
            'confirmed',
            'regex:/[a-z]/',
            'regex:/[A-Z]/',
            'regex:/[0-9]/',
            'regex:/[@$!%*#?&._\-]/',
        ];
    }

    public static function messages(): array
    {
        return [
            'password.min'       => 'Password must be at least 8 characters.',
            'password.regex'     => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character. Example: Lens@2026',
            'password.confirmed' => 'Passwords do not match.',
        ];
    }
}
