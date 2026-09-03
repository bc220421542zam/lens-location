{{-- Rendered by the full page and by the AJAX JSON endpoint; the id keeps the
     fragment addressable across outerHTML swaps. --}}
<tbody id="ledger-tbody">
    @forelse($transactions as $t)
        @php
            $isPaid = $t->status === \App\Enums\PaymentStatus::Paid;
            // Labels are ledger-specific ("Received" / "Transferred"); colours
            // reuse the app-wide badge palette from the payment enums.
            $paymentBadge = match ($t->status) {
                \App\Enums\PaymentStatus::Paid     => ['bg-green-100 text-green-700', 'bg-green-600', 'Received'],
                \App\Enums\PaymentStatus::Pending  => ['bg-yellow-100 text-yellow-700', 'bg-yellow-600', 'Pending'],
                \App\Enums\PaymentStatus::Failed   => ['bg-red-100 text-red-600', 'bg-red-600', 'Failed'],
                \App\Enums\PaymentStatus::Refunded => ['bg-gray-100 text-gray-600', 'bg-gray-500', 'Refunded'],
            };
            $transferBadge = match ($t->payout_status) {
                \App\Enums\PayoutStatus::Held     => ['bg-gray-100 text-gray-600', 'bg-gray-500', 'On platform'],
                \App\Enums\PayoutStatus::Eligible => ['bg-yellow-100 text-yellow-700', 'bg-yellow-600', 'Pending'],
                \App\Enums\PayoutStatus::PaidOut  => ['bg-green-100 text-green-700', 'bg-green-600', 'Transferred'],
            };
        @endphp
        <tr class="border-t border-indigo-50 {{ $loop->even ? 'bg-indigo-50/40' : 'bg-white' }} hover:bg-indigo-50 transition-colors">
            <td class="py-3 px-2 text-sm whitespace-nowrap text-indigo-700">
                <a href="{{ route('admin.bookings.show', $t->booking_id) }}"
                   class="hover:text-indigo-900 hover:underline">BK-{{ str_pad((string) $t->booking_id, 5, '0', STR_PAD_LEFT) }}</a>
            </td>
            <td class="py-3 px-2 text-sm whitespace-nowrap font-medium text-indigo-900">{{ $t->customer?->name ?? '—' }}</td>
            <td class="py-3 px-2 text-sm whitespace-nowrap text-indigo-700">{{ $t->booking?->booking_date?->format('M d, Y') ?? '—' }}</td>
            <td class="py-3 px-2 text-sm whitespace-nowrap font-medium tabular-nums text-indigo-900 text-right">Rs {{ number_format($t->amount) }}</td>
            <td class="py-3 px-2 text-sm whitespace-nowrap">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap {{ $paymentBadge[0] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $paymentBadge[1] }}"></span>
                    {{ $paymentBadge[2] }}
                </span>
                @if ($t->disputed_at)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap bg-red-100 text-red-600"
                          title="Disputed {{ $t->disputed_at->format('M d, Y') }} - escrow held pending admin review">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                        Disputed
                    </span>
                @endif
            </td>
            <td class="py-3 px-2 text-sm whitespace-nowrap text-indigo-700">{{ $t->paid_at?->format('M d, Y') ?? '—' }}</td>
            <td class="py-3 px-2 text-sm whitespace-nowrap text-indigo-700 text-right">{{ $isPaid && $t->held_since ? $t->held_since->startOfDay()->diffInDays(now()->startOfDay()).' days' : '—' }}</td>
            <td class="py-3 px-2 text-sm whitespace-nowrap tabular-nums text-indigo-700 text-right">Rs {{ number_format($t->platform_commission) }}</td>
            <td class="py-3 px-2 text-sm whitespace-nowrap tabular-nums text-indigo-700 text-right">Rs {{ number_format($t->owner_payout_amount) }}</td>
            <td class="py-3 px-2 text-sm whitespace-nowrap">
                @if ($t->disputed_at)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap bg-red-100 text-red-600">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                        Held - disputed
                    </span>
                @elseif ($isPaid)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap {{ $transferBadge[0] }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $transferBadge[1] }}"></span>
                        {{ $transferBadge[2] }}
                    </span>
                @else
                    <span class="text-indigo-300">—</span>
                @endif
            </td>
            <td class="py-3 px-2 text-sm whitespace-nowrap text-indigo-700">
                {{ $t->transferred_at?->format('M d, Y') ?? $t->latestPayout()?->processed_at?->format('M d, Y') ?? '—' }}
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="11" class="py-6 text-center text-indigo-400 text-sm">No bookings found.</td>
        </tr>
    @endforelse
</tbody>
