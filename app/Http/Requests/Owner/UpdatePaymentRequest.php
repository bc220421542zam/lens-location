<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'account_holder' => ['nullable', 'string', 'max:255'],
            'bank_name'      => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:255'],
            'wallet_type'    => ['nullable', 'string', 'max:100'],
            'wallet_number'  => ['nullable', 'string', 'max:20'],
        ];
    }
}
