<?php

namespace App\Http\Requests\Customer;

use App\Models\Booking;
use Carbon\Carbon;
use Closure;
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
            'booking_date' => [
                'required',
                'date',
                // The input is Asia/Karachi wall time; plain `after:now` would
                // compare it as UTC and accept slots up to 5 hours in the past.
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (Carbon::parse($value, Booking::BOOKING_DISPLAY_TIMEZONE)->isPast()) {
                        $fail('Booking date must be in the future.');
                    }
                },
            ],
            'hours'        => ['required', 'integer', 'min:1', 'max:24'],
            'shoot_type'   => ['nullable', 'string', 'max:100'],
        ];
    }
}
