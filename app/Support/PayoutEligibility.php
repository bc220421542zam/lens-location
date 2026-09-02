<?php

namespace App\Support;

use App\Enums\PaymentStatus;
use App\Enums\PayoutStatus;
use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Flips a completed booking's paid transaction(s) from escrow `held` to
 * `eligible`, stamping the escrow split onto the transaction at the same time.
 *
 * The split is COPIED from platform_fee/owner_earning (what the customer was
 * actually charged under at checkout) rather than recomputed with the current
 * commission rate - the money in escrow must reconcile with the charge, and
 * the stored columns are already rate-change-proof.
 *
 * Idempotent by design: the `payout_status = held` guard means re-runs touch
 * nothing. One scoped UPDATE, no model hydration.
 */
final class PayoutEligibility
{
    public static function markEligibleForBooking(Booking $booking): int
    {
        return self::markEligible(collect([$booking->id]));
    }

    public static function markEligible(Collection $bookingIds): int
    {
        if ($bookingIds->isEmpty()) {
            return 0;
        }

        return Transaction::whereIn('booking_id', $bookingIds)
            ->where('status', PaymentStatus::Paid)
            ->where('payout_status', PayoutStatus::Held)
            ->update([
                'payout_status'       => PayoutStatus::Eligible,
                'platform_commission' => DB::raw('platform_fee'),
                'owner_payout_amount' => DB::raw('owner_earning'),
            ]);
    }
}
