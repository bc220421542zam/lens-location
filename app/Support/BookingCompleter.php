<?php

namespace App\Support;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Location;
use Illuminate\Database\Eloquent\Builder;

/**
 * Moves paid bookings to `completed`.
 *
 * The webhook does this in real time when a payment lands, so this is the
 * backstop for bookings that were paid while the webhook never arrived - the
 * Stripe CLI not running locally, a delivery failure, or a payment taken before
 * automatic completion existed. Called on page load rather than from a
 * scheduler, so no cron setup is required.
 *
 * One scoped UPDATE, no model hydration.
 */
class BookingCompleter
{
    public static function forCustomer(int $customerId): int
    {
        return self::complete(Booking::where('customer_id', $customerId));
    }

    public static function forOwner(int $ownerId): int
    {
        return self::complete(
            Booking::whereIn('location_id', Location::select('id')->where('user_id', $ownerId)),
        );
    }

    /**
     * Only `confirmed` bookings advance - this must never resurrect one the
     * customer or owner cancelled, or re-complete a refunded one.
     */
    private static function complete(Builder $query): int
    {
        return $query
            ->where('status', BookingStatus::Confirmed)
            ->whereHas('transactions', fn ($q) => $q->where('status', PaymentStatus::Paid))
            ->update(['status' => BookingStatus::Completed]);
    }
}
