<?php

namespace App\Http\Controllers\Customer;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Support\BookingCompleter;
use App\Support\TimeSeriesCounts;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const CHART_MONTHS = 6;

    private const UPCOMING_LIMIT = 5;

    public function index(): View
    {
        $customerId = auth()->id();

        BookingCompleter::forCustomer($customerId);

        // Previously every booking row was hydrated into memory just to count
        // and sum them; the database does both in a single pass now. Upcoming
        // counts unpaid and paid future bookings (payment moves a booking
        // straight to `completed`); completed/spent count every paid booking,
        // whether the date has arrived (`visited`) or not.
        $totals = Booking::where('customer_id', $customerId)
            ->selectRaw(
                'COUNT(*) as total_count,
                 COUNT(CASE WHEN status IN (?, ?, ?) AND booking_date >= ? THEN 1 END) as upcoming_count,
                 COUNT(CASE WHEN status IN (?, ?) THEN 1 END) as completed_count,
                 COUNT(CASE WHEN status = ? THEN 1 END) as pending_count,
                 COUNT(CASE WHEN status = ? THEN 1 END) as cancelled_count,
                 COALESCE(SUM(CASE WHEN status IN (?, ?) THEN total_price END), 0) as completed_spend',
                [
                    BookingStatus::Pending->value, BookingStatus::Confirmed->value, BookingStatus::Completed->value, now()->toDateTimeString(),
                    BookingStatus::Completed->value, BookingStatus::Visited->value,
                    BookingStatus::Pending->value,
                    BookingStatus::Cancelled->value,
                    BookingStatus::Completed->value, BookingStatus::Visited->value,
                ]
            )
            ->first();

        $stats = [
            'total'     => (int) $totals->total_count,
            'upcoming'  => (int) $totals->upcoming_count,
            'completed' => (int) $totals->completed_count,
            'pending'   => (int) $totals->pending_count,
            'cancelled' => (int) $totals->cancelled_count,
            'spent'     => (float) $totals->completed_spend,
        ];

        $upcoming = Booking::with('location.owner')
            ->where('customer_id', $customerId)
            ->whereIn('status', [BookingStatus::Pending, BookingStatus::Confirmed, BookingStatus::Completed])
            ->where('booking_date', '>=', now())
            ->orderBy('booking_date')
            ->take(self::UPCOMING_LIMIT)
            ->get();

        // One grouped query instead of six month-by-month fetches.
        $monthly = TimeSeriesCounts::monthly(
            Booking::where('customer_id', $customerId),
            self::CHART_MONTHS,
            [
                'upcoming'  => ['status', BookingStatus::Confirmed->value],
                'completed' => ['status', [BookingStatus::Completed->value, BookingStatus::Visited->value]],
                'cancelled' => ['status', BookingStatus::Cancelled->value],
            ]
        );

        $chartData = [
            'labels'    => $monthly['labels'],
            'total'     => $monthly['series']['total'],
            'upcoming'  => $monthly['series']['upcoming'],
            'completed' => $monthly['series']['completed'],
            'cancelled' => $monthly['series']['cancelled'],
            'stats'     => [
                'total'     => $stats['total'],
                'upcoming'  => $stats['upcoming'],
                'completed' => $stats['completed'],
                'cancelled' => $stats['cancelled'],
            ],
        ];

        return view('customer.dashboard', compact('stats', 'upcoming', 'chartData'));
    }
}
