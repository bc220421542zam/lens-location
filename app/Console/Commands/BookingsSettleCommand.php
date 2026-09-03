<?php

namespace App\Console\Commands;

use App\Support\BookingCompleter;
use Illuminate\Console\Command;

/**
 * Periodic booking settlement, run by the scheduler:
 *
 *  - Completed + paid + booking date passed   -> visited, escrow released
 *  - Confirmed + paid + booking date passed   -> visited (payment the webhook
 *    never saw), escrow released
 *  - Confirmed + unpaid + booking date passed -> expired, never eligible for payout
 *
 * Both moves live in BookingCompleter (the page-load backstop), so this
 * command is the cron-driven whole-platform pass the spec calls for. Idempotent
 * by design - re-running touches nothing already settled.
 */
class BookingsSettleCommand extends Command
{
    protected $signature = 'bookings:settle';

    protected $description = 'Expire unpaid past-due bookings and mark paid past-due bookings visited';

    public function handle(): int
    {
        $expired = BookingCompleter::expireUnpaidForAll();
        $visited = BookingCompleter::forAll();

        $this->info("Settled bookings: {$visited} visited, {$expired} expired.");

        return self::SUCCESS;
    }
}
