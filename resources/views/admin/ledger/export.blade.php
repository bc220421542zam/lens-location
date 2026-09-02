<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Booking &amp; Payment Ledger</title>
<style>
    body { font-family: DejaVu Sans, sans-serif; color: #16233D; font-size: 11px; margin: 24px; }
    .header { border-bottom: 2px solid #0F2D5C; padding-bottom: 12px; margin-bottom: 16px; }
    .header h1 { font-size: 18px; margin: 0; color: #0F2D5C; }
    .header .meta { font-size: 10px; color: #5B6B87; margin-top: 4px; }
    .cards { width: 100%; margin-bottom: 18px; border-collapse: separate; border-spacing: 8px 0; }
    .cards td { width: 25%; border: 1px solid #E1E8F4; border-radius: 8px; padding: 10px 12px; }
    .cards .label { font-size: 9px; color: #5B6B87; text-transform: uppercase; }
    .cards .value { font-size: 16px; font-weight: bold; color: #0F2D5C; }
    h2 { font-size: 13px; color: #0F2D5C; margin: 18px 0 8px; }
    table.data { width: 100%; border-collapse: collapse; }
    table.data th { text-align: left; font-size: 9px; text-transform: uppercase; color: #5B6B87; border-bottom: 1px solid #E1E8F4; padding: 6px 8px; }
    table.data td { padding: 7px 8px; border-bottom: 1px solid #E1E8F4; }
    table.data tr:nth-child(even) td { background: #F4F8FE; }
    .footer { margin-top: 22px; font-size: 9px; color: #5B6B87; border-top: 1px solid #E1E8F4; padding-top: 8px; }
    .empty { text-align: center; color: #5B6B87; padding: 16px; }
</style>
</head>
<body>

<div class="header">
    <h1>Booking &amp; Payment Dashboard</h1>
    <div class="meta">
        Period view: {{ ucfirst($period) }} &middot;
        Generated: {{ now()->format('M d, Y H:i') }} &middot;
        @if (request()->filled('from') || request()->filled('to'))
            Date range: {{ request('from', 'any') }} &ndash; {{ request('to', 'any') }} &middot;
        @endif
        @if (request()->filled('status'))
            Status: {{ ucfirst(request('status')) }}
        @endif
    </div>
</div>

<table class="cards">
    <tr>
        <td>
            <div class="label">Total bookings</div>
            <div class="value">{{ number_format($summary['bookings']) }}</div>
        </td>
        <td>
            <div class="label">Total amount received</div>
            <div class="value">Rs {{ number_format($summary['received']) }}</div>
        </td>
        <td>
            <div class="label">Platform commission</div>
            <div class="value">Rs {{ number_format($summary['commission']) }}</div>
        </td>
        <td>
            <div class="label">Paid to owners</div>
            <div class="value">Rs {{ number_format($summary['paid_to_owners']) }}</div>
        </td>
    </tr>
</table>

<h2>Period totals</h2>
<table class="data">
    <thead>
        <tr><th>Period</th><th>Collected</th><th>Commission</th><th>Owner payout</th></tr>
    </thead>
    <tbody>
        @foreach ($trend as $bucket)
            <tr>
                <td>{{ $bucket['label'] }}</td>
                <td>Rs {{ number_format($bucket['collected']) }}</td>
                <td>Rs {{ number_format($bucket['commission']) }}</td>
                <td>Rs {{ number_format($bucket['payout']) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h2>Bookings &amp; payments ({{ $transactions->count() }})</h2>
<table class="data">
    <thead>
        <tr>
            <th>Booking ID</th>
            <th>Customer</th>
            <th>Booking date</th>
            <th>Amount paid</th>
            <th>Payment status</th>
            <th>Received on</th>
            <th>Days held</th>
            <th>Commission</th>
            <th>Owner share</th>
            <th>Transfer status</th>
            <th>Transferred on</th>
        </tr>
    </thead>
    <tbody>
        @forelse($transactions as $t)
            @php
                $isPaid = $t->status === \App\Enums\PaymentStatus::Paid;
            @endphp
            <tr>
                <td>BK-{{ str_pad((string) $t->booking_id, 5, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $t->customer?->name ?? '-' }}</td>
                <td>{{ $t->booking?->booking_date?->format('M d, Y') ?? '-' }}</td>
                <td>Rs {{ number_format($t->amount) }}</td>
                <td>{{ $t->status->label() }}</td>
                <td>{{ $t->paid_at?->format('M d, Y') ?? '-' }}</td>
                <td>{{ $isPaid && $t->held_since ? $t->held_since->startOfDay()->diffInDays(now()->startOfDay()).' days' : '-' }}</td>
                <td>Rs {{ number_format($t->platform_commission) }}</td>
                <td>Rs {{ number_format($t->owner_payout_amount) }}</td>
                <td>{{ $t->disputed_at ? 'Held - disputed' : ($isPaid ? $t->payout_status->label() : '-') }}</td>
                <td>{{ $t->transferred_at?->format('M d, Y') ?? $t->latestPayout()?->processed_at?->format('M d, Y') ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="11" class="empty">No bookings found.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    LensLocation &middot; Escrow payout ledger &middot; Page generated automatically.
</div>

</body>
</html>
