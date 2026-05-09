<?php

namespace App\Http\Requests\Photographer;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'booking_date' => ['required', 'date', 'after:now'],
            'hours'        => ['required', 'integer', 'min:1', 'max:24'],
            'shoot_type'   => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'booking_date.after' => 'Booking date must be in the future.',
        ];
    }
}
