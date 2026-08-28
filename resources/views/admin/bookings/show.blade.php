<x-layouts.admin>
    <x-topbar
        title="Booking Details"
        description="Everything about this booking">
        <x-slot:actions>
        <a href="{{ route('admin.bookings') }}"
           class="btn btn-transition w-full sm:w-auto px-4 text-center">
            &larr; All Bookings
        </a>
        </x-slot:actions>
    </x-topbar>

    <x-success class="mb-4" />
    <x-error class="mb-4" />

    <div class="max-w-4xl mx-auto">

        <div class="shade card card-transition bg-[#EEEFF7] p-6 rounded-2xl">

            {{-- STATUS ROW --}}
            <div class="flex flex-wrap items-center gap-2 mb-6">
                <span class="w-fit text-xs font-semibold px-2.5 py-1 rounded-full {{ $booking->status->badgeClasses() }}">
                    {{ $booking->status->label() }}
                </span>
                @if ($booking->transactions->contains(fn ($t) => $t->status->value === 'paid'))
                    <span class="w-fit text-xs font-semibold px-2.5 py-1 rounded-full bg-green-100 text-green-700">
                        <i class="fa-solid fa-circle-check mr-1"></i>Paid
                    </span>
                @endif
            </div>

            {{-- DETAILS --}}
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div class="sm:col-span-2">
                    <dt class="text-xs uppercase underline text-indigo-800">Listing</dt>
                    <dd class="font-medium text-indigo-900">
                        {{ $booking->location?->title ?? 'Deleted listing' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs uppercase underline text-indigo-800">Customer</dt>
                    <dd class="font-medium text-indigo-900">
                        @if($booking->customer)
                            <a href="{{ route('admin.users.detail', $booking->customer->id) }}"
                               class="text-indigo-700 hover:underline">{{ $booking->customer->name }}</a>
                        @else
                            —
                        @endif
                    </dd>
                    <dd class="text-xs text-gray-500">{{ $booking->customer?->email }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase underline text-indigo-800">Owner</dt>
                    <dd class="font-medium text-indigo-900">
                        @if($booking->location?->owner)
                            <a href="{{ route('admin.users.detail', $booking->location->owner->id) }}"
                               class="text-indigo-700 hover:underline">{{ $booking->location->owner->name }}</a>
                        @else
                            —
                        @endif
                    </dd>
                    <dd class="text-xs text-gray-500">{{ $booking->location?->owner?->email }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase underline text-indigo-800">Date</dt>
                    <dd class="font-medium text-indigo-900">{{ $booking->booking_date->format('M d, Y · g:i A') }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase underline text-indigo-800">Duration</dt>
                    <dd class="font-medium text-indigo-900">{{ $booking->hours }} hr{{ $booking->hours > 1 ? 's' : '' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase underline text-indigo-800">Shoot type</dt>
                    <dd class="font-medium text-indigo-900">{{ $booking->shoot_type ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase underline text-indigo-800">Total</dt>
                    <dd class="font-medium text-indigo-900">PKR {{ number_format((float) $booking->total_price) }}</dd>
                </div>
            </dl>

            {{-- PAYMENTS --}}
            <div>
                <h3 class="text-sm font-semibold text-indigo-900 mb-2">Payments</h3>
                @if ($booking->transactions->isNotEmpty())
                    <div class="overflow-x-auto border border-indigo-100 rounded-xl bg-white">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-[11px] uppercase tracking-wide text-indigo-900 border-b border-indigo-100 bg-indigo-50/60">
                                    <th class="py-2 px-3 font-medium">Ref</th>
                                    <th class="py-2 px-3 font-medium">Amount</th>
                                    <th class="py-2 px-3 font-medium">Commission</th>
                                    <th class="py-2 px-3 font-medium">Owner Share</th>
                                    <th class="py-2 px-3 font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($booking->transactions as $t)
                                    <tr class="border-t border-indigo-50">
                                        <td class="py-2 px-3 text-sm text-indigo-700">{{ $t->reference() ?? '—' }}</td>
                                        <td class="py-2 px-3 text-sm text-indigo-700">Rs. {{ number_format((float) $t->amount, 2) }}</td>
                                        <td class="py-2 px-3 text-sm text-indigo-700">Rs. {{ number_format((float) $t->platform_fee, 2) }}</td>
                                        <td class="py-2 px-3 text-sm text-indigo-700">Rs. {{ number_format((float) $t->owner_earning, 2) }}</td>
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

        </div>
    </div>
</x-layouts.admin>
