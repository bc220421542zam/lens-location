<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PayoutBatchStatus;
use App\Enums\PaymentStatus;
use App\Enums\PayoutStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payout;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Booking & Payment Dashboard: the escrow ledger. Shows the whole per-booking
 * lifecycle - paid, held in escrow, completed/eligible, batched, paid out -
 * with summary cards, a filterable transaction table and a commission/payout
 * trend, plus a PDF export of the same data.
 */
class LedgerController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        // Visiting clears the Ledger sidebar dot until the next payout owed.
        // Skipped on AJAX refreshes - one less DB write per keystroke.
        if (! $request->expectsJson()) {
            $request->user()->markSectionViewed('admin.ledger');
        }

        $data = $this->data($request);

        // Live updates: Alpine fetches this route with `Accept: application/json`
        // and swaps the returned fragments (table, pagination, period totals).
        if ($request->expectsJson()) {
            return response()->json([
                'summary'        => $data['summary'],
                'trend'          => $data['trend'],
                'period'         => $data['period'],
                'trend_subtitle' => $data['trendSubtitle'],
                'count'          => $data['transactions']->total(),
                'html'           => view('admin.ledger.partials.table', $data)->render(),
                'pagination'     => view('admin.ledger.partials.pagination', $data)->render(),
                'report_html'    => view('admin.ledger.partials.report', $data)->render(),
                'url'            => $request->fullUrl(),
            ], 200, [], JSON_PRESERVE_ZERO_FRACTION);
        }

        return view('admin.ledger', $data);
    }

    public function export(Request $request)
    {
        $data = $this->data($request, paginate: false);

        return Pdf::loadView('admin.ledger.export', $data)
            ->setPaper('a4', 'landscape')
            ->download('ledger-'.$data['period'].'-'.now()->format('Y-m-d').'.pdf');
    }

    private function data(Request $request, bool $paginate = true): array
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'from'   => 'nullable|date',
            'to'     => 'nullable|date|after_or_equal:from',
            'status' => 'nullable|in:paid,pending,failed,refunded',
            'period' => 'nullable|in:weekly,monthly',
        ]);

        $period = $request->string('period')->toString() ?: 'weekly';

        $query = $this->applyFilters(
            Transaction::query()->with(['booking', 'customer', 'owner', 'payouts']),
            $request,
        )->latest('paid_at');

        $transactions = $paginate
            ? $query->paginate(10)->withQueryString()
            : $query->get();

        $summary = $this->summary($request);

        $trend = $this->trendData($period);

        $trendSubtitle = $period === 'weekly'
            ? 'Weekly totals for the current month'
            : 'Monthly totals for the last six months';

        return compact('transactions', 'summary', 'trend', 'period', 'trendSubtitle');
    }

    /**
     * The filter closures shared by the table query and the summary cards, so
     * the cards always reflect exactly what the table is filtered to.
     */
    private function applySearch(Builder $query, Request $request): Builder
    {
        return $query->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
            $term = '%'.$request->string('search').'%';

            $q->where('booking_id', 'like', $term)
                ->orWhereHas('customer', fn ($q) => $q
                    ->where('first_name', 'like', $term)
                    ->orWhere('last_name', 'like', $term));
        }));
    }

    private function applyDateRange(Builder $query, Request $request): Builder
    {
        return $query
            ->when($request->filled('from'), fn ($q) => $q->where('paid_at', '>=', $request->date('from')->startOfDay()))
            ->when($request->filled('to'), fn ($q) => $q->where('paid_at', '<=', $request->date('to')->endOfDay()));
    }

    private function applyStatus(Builder $query, Request $request): Builder
    {
        return $query->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')));
    }

    private function applyFilters(Builder $query, Request $request): Builder
    {
        return $this->applyStatus($this->applyDateRange($this->applySearch($query, $request), $request), $request);
    }

    /**
     * Totals for the four summary cards. Follows the same search/status/date
     * filters as the table; with no dates given it defaults to the current
     * month, preserving the original "this month" behavior.
     */
    private function summary(Request $request): array
    {
        $from = $request->filled('from')
            ? $request->date('from')->startOfDay()
            : now()->copy()->startOfMonth();
        $to = $request->filled('to')
            ? $request->date('to')->endOfDay()
            : now()->copy()->endOfMonth();

        // A status filter overrides the "paid" baseline; without one, the cards
        // mean money actually received.
        $status = $request->filled('status')
            ? PaymentStatus::from($request->string('status'))
            : PaymentStatus::Paid;

        $paid = Transaction::query()
            ->where('status', $status)
            ->whereBetween('paid_at', [$from, $to]);
        $this->applySearch($paid, $request);

        $paidOut = Transaction::query()
            ->where('payout_status', PayoutStatus::PaidOut)
            ->whereBetween('paid_at', [$from, $to]);
        $this->applySearch($paidOut, $request);
        if ($request->filled('status')) {
            // Only "paid" rows are ever PaidOut, so a status filter applies here too.
            $this->applyStatus($paidOut, $request);
        }

        $rangeLabel = match (true) {
            $request->filled('from') && $request->filled('to')
                => $from->format('M d, Y').' – '.$to->format('M d, Y'),
            $request->filled('from') => 'From '.$from->format('M d, Y'),
            $request->filled('to')   => 'Until '.$to->format('M d, Y'),
            default                  => 'This month',
        };

        return [
            'bookings'       => Booking::whereHas('transactions', function ($q) use ($status, $from, $to, $request) {
                $q->where('status', $status)->whereBetween('paid_at', [$from, $to]);
                $this->applySearch($q, $request);
            })->count(),
            'received'       => (float) (clone $paid)->sum('amount'),
            'commission'     => (float) (clone $paid)->sum('platform_commission'),
            'paid_to_owners' => (float) $paidOut->sum('owner_payout_amount'),
            'range_label'    => $rangeLabel,
        ];
    }

    /**
     * Buckets for the trend chart and period-totals table. Weekly buckets are
     * the (up to five) 7-day slices of the current month; monthly buckets are
     * the last six calendar months. Implemented with Carbon range pairs +
     * whereBetween so it stays portable across MySQL and SQLite.
     */
    private function trendData(string $period): array
    {
        if ($period === 'monthly') {
            $buckets = collect(range(5, 0))->map(fn ($i) => [
                'label' => now()->copy()->subMonthsNoOverflow($i)->format('M'),
                'start' => now()->copy()->subMonthsNoOverflow($i)->startOfMonth(),
                'end'   => now()->copy()->subMonthsNoOverflow($i)->endOfMonth(),
            ]);
        } else {
            $monthStart = now()->copy()->startOfMonth();
            $monthEnd = now()->copy()->endOfMonth();

            $buckets = collect(range(0, 4))->map(function ($i) use ($monthStart, $monthEnd) {
                $start = $monthStart->copy()->addDays($i * 7);
                $end = $start->copy()->addDays(6)->min($monthEnd);

                return ['label' => 'Week '.($i + 1), 'start' => $start, 'end' => $end];
            })->filter(fn ($bucket) => $bucket['start']->lte($monthEnd));
        }

        return $buckets->map(function ($bucket) {
            $paid = Transaction::query()
                ->where('status', PaymentStatus::Paid)
                ->whereBetween('paid_at', [$bucket['start'], $bucket['end']]);

            return [
                'label'      => $bucket['label'],
                'collected'  => (float) (clone $paid)->sum('amount'),
                'commission' => (float) (clone $paid)->sum('platform_commission'),
                'payout'     => (float) Payout::query()
                    ->where('status', PayoutBatchStatus::Processed)
                    ->whereBetween('processed_at', [$bucket['start'], $bucket['end']])
                    ->sum('total_amount'),
            ];
        })->values()->all();
    }
}
