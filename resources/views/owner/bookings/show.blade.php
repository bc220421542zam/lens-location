<x-layouts.owner>
<div class="page max-w-3xl">

    @php
        $status   = $booking->status->value;
        $isPaid   = $booking->transactions->contains(fn ($t) => $t->status->value === 'paid');
        $canAccept = $status === 'pending';
    @endphp

    <x-topbar
        title="Booking Details"
        description="Everything about this booking">
        <x-slot:actions>
        <a href="{{ route('owner.bookings') }}"
           class="btn btn-transition w-full sm:w-auto px-4 text-center">
            &larr; My Bookings
        </a>
        </x-slot:actions>
    </x-topbar>

    @if (session('success'))
        <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-700 text-sm">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    <div class="card chart-transition rounded-2xl border border-indigo-400 p-0 overflow-hidden">

        {{-- COVER --}}
        <div class="w-full h-56 bg-gray-100">
            @if ($booking->location?->image)
                <img src="{{ asset('storage/'.$booking->location->image) }}"
                     alt="" loading="lazy" decoding="async"
                     class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <i class="fa-solid fa-camera text-4xl text-gray-300"></i>
                </div>
            @endif
        </div>

        <div class="p-6">

            {{-- STATUS ROW --}}
            <div class="flex flex-wrap items-center gap-2 mb-4">
                <span class="w-fit text-xs font-semibold px-2.5 py-1 rounded-full {{ $booking->status->badgeClasses() }}">
                    {{ $booking->status->label() }}
                </span>
                @if ($isPaid)
                    <span class="w-fit text-xs font-semibold px-2.5 py-1 rounded-full bg-green-100 text-green-700">
                        <i class="fa-solid fa-circle-check mr-1"></i>Paid
                    </span>
                @endif
            </div>

            {{-- DETAILS --}}
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div class="sm:col-span-2">
                    <dt class="text-xs uppercase underline text-indigo-900">Listing</dt>
                    <dd class="font-medium text-indigo-900">
                        {{ $booking->location?->title ?? 'Deleted listing' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs uppercase underline text-indigo-900">Customer</dt>
                    <dd class="font-medium text-indigo-900">{{ $booking->customer?->name ?? 'Unknown' }}</dd>
                    <dd class="text-xs text-gray-500">{{ $booking->customer?->email }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase underline text-indigo-900">Phone</dt>
                    <dd class="font-medium text-indigo-900">{{ $booking->customer?->phone ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase underline text-indigo-900">Date</dt>
                    <dd class="font-medium text-indigo-900">{{ $booking->booking_date->format('M d, Y · g:i A') }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase underline text-indigo-900">Duration</dt>
                    <dd class="font-medium text-indigo-900">{{ $booking->hours }} hr{{ $booking->hours > 1 ? 's' : '' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase underline text-indigo-900">Shoot type</dt>
                    <dd class="font-medium text-indigo-900">{{ $booking->shoot_type ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase underline text-indigo-900">Total</dt>
                    <dd class="font-medium text-indigo-900">PKR {{ number_format((float) $booking->total_price) }}</dd>
                </div>
            </dl>

            {{-- LOCATION ON MAP --}}
            <x-location-map
                :latitude="$booking->location?->latitude"
                :longitude="$booking->location?->longitude"
                :address="$booking->location ? $booking->location->address . ', ' . $booking->location->city : null" />

            {{-- PAYMENTS --}}
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-indigo-900 mb-2">Payments</h3>
                @if ($booking->transactions->isNotEmpty())
                    <div class="overflow-x-auto border border-indigo-100 rounded-xl">
                        <table class="table-clean w-full text-left">
                            <thead>
                                <tr class="text-[11px] uppercase tracking-wide text-indigo-900 border-b border-indigo-100 bg-indigo-50/60">
                                    <th class="py-2 px-3 font-medium">Ref</th>
                                    <th class="py-2 px-3 font-medium text-right">Amount</th>
                                    <th class="py-2 px-3 font-medium text-right">Your Share</th>
                                    <th class="py-2 px-3 font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($booking->transactions as $t)
                                    <tr class="border-t border-indigo-50 {{ $loop->even ? 'bg-indigo-50/40' : 'bg-white' }} hover:bg-indigo-50 transition-colors">
                                        <td class="py-2 px-3 text-sm text-indigo-700">{{ $t->reference() ?? '—' }}</td>
                                        <td class="py-2 px-3 text-sm tabular-nums text-indigo-700 text-right">Rs. {{ number_format((float) $t->amount, 2) }}</td>
                                        <td class="py-2 px-3 text-sm tabular-nums text-indigo-700 text-right">Rs. {{ number_format((float) $t->owner_earning, 2) }}</td>
                                        <td class="py-2 px-3 text-sm">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $t->status->badgeClasses() }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $t->status->dotClasses() }}"></span>
                                                {{ $t->status->label() }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-400">No payments yet.</p>
                @endif
            </div>

            {{-- ACTIONS --}}
            @if ($canAccept)
                <div class="flex flex-col gap-2">
                    <form method="POST" action="{{ route('owner.bookings.accept', $booking->id) }}">
                        @csrf
                        <button type="submit"
                                class="action-btn publish grow-0 basis-auto w-full whitespace-nowrap">
                            Accept
                        </button>
                    </form>
                    <form method="POST" action="{{ route('owner.bookings.reject', $booking->id) }}"
                          onsubmit="return confirm('Reject this booking?')">
                        @csrf
                        <button type="submit"
                                class="action-btn danger grow-0 basis-auto w-full whitespace-nowrap">
                            Reject
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>

</div>
</x-layouts.owner>
