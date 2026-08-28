<?php

namespace App\Support;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Location;
use Illuminate\Database\Eloquent\Builder;

/**
 * Moves paid bookings to `completed` once their shoot has finished.
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
     * Whole-platform pass - the admin bookings page uses this since it has no
     * single customer or owner scope.
     */
    public static function forAll(): int
    {
        return self::complete(Booking::query());
    }

    /**
     * Only `confirmed` bookings advance - this must never resurrect one the
     * customer or owner cancelled, or re-complete a refunded one.
     *
     * `serviceEnded()` is what keeps payment from standing in for delivery: a
     * shoot booked for next month is paid today but stays `confirmed` until it
     * has actually happened, so the review action can't open early.
     */
    private static function complete(Builder $query): int
    {
        return $query
            ->where('status', BookingStatus::Confirmed)
            ->serviceEnded()
            ->whereHas('transactions', fn ($q) => $q->where('status', PaymentStatus::Paid))
            ->update(['status' => BookingStatus::Completed]);
    }
}
