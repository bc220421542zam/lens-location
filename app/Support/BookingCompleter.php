<?php

namespace App\Support;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\PayoutStatus;
use App\Models\Booking;
use App\Models\Location;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Booking lifecycle backstop: expires unpaid bookings and visits paid ones
 * once their booking date has passed.
 *
 * The lifecycle:
 *
 *  pending -> confirmed (owner accepts) -> completed (customer pays)
 *          -> visited (booking date has passed, escrow released)
 *
 * with `expired` for a confirmed booking whose date passed without payment.
 *
 * The `bookings:settle` scheduler runs this whole-platform periodically; the
 * controllers call the scoped variants on page load so a customer or owner
 * opening their bookings sees an up-to-date status even when the scheduler
 * isn't running (e.g. the Stripe CLI isn't up locally, or a payment landed
 * while the webhook never arrived).
 *
 * Both moves are one scoped UPDATE each, no model hydration. Visiting also
 * sweeps already-visited bookings whose escrow is still eligible, so a
 * transfer skipped earlier (onboarding incomplete, or an older code version)
 * goes out on the next pass instead of waiting for the payout batch.
 */
class BookingCompleter
{
    // ------------------------------------------------------------------- Visit

    public static function forCustomer(int $customerId): int
    {
        return self::visit(Booking::where('customer_id', $customerId));
    }

    public static function forOwner(int $ownerId): int
    {
        return self::visit(
            Booking::whereIn('location_id', Location::select('id')->where('user_id', $ownerId)),
        );
    }

    /**
     * Whole-platform pass - the admin bookings page uses this since it has no
     * single customer or owner scope.
     */
    public static function forAll(): int
    {
        return self::visit(Booking::query());
    }

    /**
     * Only paid bookings advance - and only from `confirmed` or `completed`.
     * `completed` is the normal post-payment state; `confirmed` covers a
     * payment the webhook never saw (the settle pass catches up straight to
     * visited). This must never resurrect one the customer or owner cancelled,
     * or re-visit a refunded one.
     *
     * `started()` implements the "visited" rule: the moment the booking date
     * has arrived the slot is honored (or lost to a no-show), because the
     * owner can no longer rebook it - payout is due from the start time, not
     * the end of the shoot.
     *
     * A disputed transaction keeps its booking out of this pass - the money
     * stays in escrow until an admin resolves the dispute.
     *
     * Visiting a booking also releases its escrow: the paid transaction flips
     * `held` -> `eligible` and gets its 90/10 split stamped (PayoutEligibility),
     * then the owner's share is transferred to their connected Stripe account
     * straight away (PayoutTransfer). A transfer failure leaves the money
     * eligible for the payout batch's retry - the visit itself never fails.
     */
    private static function visit(Builder $query): int
    {
        $bookingIds = (clone $query)
            ->whereIn('status', [BookingStatus::Confirmed, BookingStatus::Completed])
            ->started()
            ->whereHas('transactions', fn ($q) => $q->where('status', PaymentStatus::Paid))
            ->whereDoesntHave('transactions', fn ($q) => $q->whereNotNull('disputed_at'))
            ->pluck('id');

        $count = 0;

        if ($bookingIds->isNotEmpty()) {
            $count = Booking::whereIn('id', $bookingIds)->update(['status' => BookingStatus::Visited]);

            PayoutEligibility::markEligible($bookingIds);

            PayoutTransfer::forBookings($bookingIds);
        }

        // Catch-up sweep: a booking visited earlier can still hold an
        // eligible transaction (the transfer was skipped while onboarding was
        // incomplete, or the visit ran before the immediate-transfer call
        // existed). The main pass never re-selects visited rows, so sweep
        // them here - the weekly batch stays the last-line retry, not the
        // only one. The bookings this pass just visited are excluded: they
        // already got their transfer attempt above, and a failure there
        // leaves them for the batch, not for an instant second try.
        self::transferEligibleForVisited($query, $bookingIds);

        return $count;
    }

    /**
     * Issue the transfer for every already-visited booking whose transactions
     * are still `eligible`. PayoutTransfer only picks eligible rows, so this
     * is idempotent and never touches anything held, disputed or whose owner
     * cannot receive payouts yet.
     */
    private static function transferEligibleForVisited(Builder $query, Collection $exclude): int
    {
        $bookingIds = (clone $query)
            ->where('status', BookingStatus::Visited)
            ->when($exclude->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $exclude))
            ->whereHas('transactions', fn ($q) => $q
                ->where('status', PaymentStatus::Paid)
                ->where('payout_status', PayoutStatus::Eligible)
                ->whereNull('disputed_at'))
            ->pluck('id');

        return PayoutTransfer::forBookings($bookingIds);
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
     * decision, and cancelled/completed/visited ones are already terminal.
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
