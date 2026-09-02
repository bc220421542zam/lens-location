<?php

use App\Console\Commands\BookingsSettleCommand;
use App\Console\Commands\PayoutsProcessCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Booking lifecycle: expire unpaid past-due bookings and mark paid ones
// visited, which releases their escrow for payout. Nothing runs without a
// scheduler - see README for the cron entry (or `php artisan schedule:work`).
Schedule::command(BookingsSettleCommand::class)
    ->hourly()
    ->withoutOverlapping();

// Escrow payouts.
Schedule::command(PayoutsProcessCommand::class, ['--period=weekly'])
    ->weekly()->mondays()->at('09:00')
    ->withoutOverlapping();

Schedule::command(PayoutsProcessCommand::class, ['--period=monthly'])
    ->monthlyOn(1, '09:00')
    ->withoutOverlapping();
