<?php

namespace App\Support;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Location;
use Illuminate\Database\Eloquent\Builder;

/**
 * Booking lifecycle backstop: expires unpaid bookings and completes paid ones
 * once their booking date has passed.
 *
 * The `bookings:settle` scheduler runs this whole-platform periodically; the
 * controllers call the scoped variants on page load so a customer or owner
 * opening their bookings sees an up-to-date status even when the scheduler
 * isn't running (e.g. the Stripe CLI isn't up locally, or a payment landed
 * while the webhook never arrived).
 *
 * Both moves are one scoped UPDATE each, no model hydration.
 */
class BookingCompleter
{
    // ---------------------------------------------------------------- Complete

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
     * `started()` implements the "visited" rule: the moment the booking date
     * has arrived the slot is honored (or lost to a no-show), because the
     * owner can no longer rebook it - payment is due from the start time, not
     * the end of the shoot.
     *
     * A disputed transaction keeps its booking out of this pass - the money
     * stays in escrow until an admin resolves the dispute.
     *
     * Completing a booking also releases its escrow: the paid transaction flips
     * `held` -> `eligible` and gets its 90/10 split stamped (PayoutEligibility).
     */
    private static function complete(Builder $query): int
    {
        $bookingIds = (clone $query)
            ->where('status', BookingStatus::Confirmed)
            ->started()
            ->whereHas('transactions', fn ($q) => $q->where('status', PaymentStatus::Paid))
            ->whereDoesntHave('transactions', fn ($q) => $q->whereNotNull('disputed_at'))
            ->pluck('id');

        if ($bookingIds->isEmpty()) {
            return 0;
        }

        $count = Booking::whereIn('id', $bookingIds)->update(['status' => BookingStatus::Completed]);

        PayoutEligibility::markEligible($bookingIds);

        return $count;
    }

    // ------------------------------------------------------------------ Expire

    public static function expireUnpaidForCustomer(int $customerId): int
    {
        return self::expireUnpaid(Booking::where('customer_id', $customerId));
    }

    public static function expireUnpaidForOwner(int $ownerId): int
    {
        return self::expireUnpaid(
            Booking::whereIn('location_id', Location::select('id')->where('user_id', $ownerId)),
        );
    }

    public static function expireUnpaidForAll(): int
    {
        return self::expireUnpaid(Booking::query());
    }

    /**
     * A confirmed booking whose date has arrived without any successful
     * payment is expired: the payment window closed at the booking date, and
     * an expired booking never enters the payout flow.
     *
     * Only `confirmed` bookings expire - pending ones still await the owner's
     * decision, and cancelled/completed ones are already terminal.
     */
    private static function expireUnpaid(Builder $query): int
    {
        $bookingIds = (clone $query)
            ->where('status', BookingStatus::Confirmed)
            ->started()
            ->whereDoesntHave('transactions', fn ($q) => $q->where('status', PaymentStatus::Paid))
            ->pluck('id');

        if ($bookingIds->isEmpty()) {
            return 0;
        }

        return Booking::whereIn('id', $bookingIds)->update(['status' => BookingStatus::Expired]);
    }
}
