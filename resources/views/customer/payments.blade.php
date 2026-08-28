<x-layouts.customer>
<div>
    <x-topbar title="Payments" description="Your payment history"></x-topbar>
    
    {{--Filters--}}
        <x-customerComp.payment-filters />

    <div class="shade card chart-transition bg-[#EEEFF7] p-4 rounded-2xl border-l-3 border-indigo-400">
        <x-success class="mb-4" />
        <x-error class="mb-4" />

        <div class="flex flex-col gap-3 mb-4">
            <h2 class="font-bold text-indigo-900 text-lg">All Payments</h2>
        </div>

        <div class="overflow-x-auto">
            <div class="bg-white rounded-xl shadow-sm border border-r-3 border-indigo-400 overflow-hidden">

                <table class="w-full text-left min-w-[640px]">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wide text-indigo-900 border-b border-indigo-100 bg-indigo-50/60">
                            <th class="py-3 px-3 font-medium">Sr. No.</th>

                            @php
                                $currentSort = request('sort');
                                $currentDirection = request('direction', 'asc');

                                $sortLink = function($column) use ($currentSort, $currentDirection) {
                                    $nextDirection = ($currentSort === $column && $currentDirection === 'asc') ? 'desc' : 'asc';
                                    return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $nextDirection]);
                                };
                            @endphp

                            <th class="px-2 font-medium">Booking</th>
                            <th class="px-2 font-medium">Listing</th>
                            <th class="px-2 font-medium">Ref</th>

                            <th class="px-2 font-medium">
                                <a href="{{ $sortLink('amount') }}" class="inline-flex items-center gap-1 hover:text-indigo-600">
                                    Amount
                                    @if($currentSort === 'amount')
                                        <i class="fa-solid fa-sort-{{ $currentDirection === 'asc' ? 'up' : 'down' }} text-[10px]"></i>
                                    @else
                                        <i class="fa-solid fa-sort text-[10px] text-indigo-300"></i>
                                    @endif
                                </a>
                            </th>

                            <th class="px-2 font-medium">
                                <a href="{{ $sortLink('status') }}" class="inline-flex items-center gap-1 hover:text-indigo-600">
                                    Status
                                    @if($currentSort === 'status')
                                        <i class="fa-solid fa-sort-{{ $currentDirection === 'asc' ? 'up' : 'down' }} text-[10px]"></i>
                                    @else
                                        <i class="fa-solid fa-sort text-[10px] text-indigo-300"></i>
                                    @endif
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $t)
                        <tr class="border-t border-indigo-50 {{ $loop->even ? 'bg-indigo-50/40' : 'bg-white' }} hover:bg-indigo-50 transition-colors">
                            <td class="py-3 px-4 text-sm text-indigo-700">
                                {{ $transactions->firstItem() + $loop->index }}
                            </td>
                            <td class="px-2 text-sm">
                                @if ($t->booking)
                                    <a href="{{ route('customer.bookings.show', $t->booking_id) }}"
                                       class="text-indigo-700 hover:underline">
                                        #{{ $t->booking_id }}
                                    </a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-2 text-sm font-medium text-indigo-900">{{ $t->booking->location->title ?? '—' }}</td>
                            <td class="px-2 text-sm text-indigo-700">{{ $t->reference() }}</td>
                            <td class="px-2 text-sm text-indigo-700">Rs. {{ number_format($t->amount, 2) }}</td>
                            <td class="px-2 text-sm">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $t->status->badgeClasses() }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $t->status->dotClasses() }}"></span>
                                    {{ $t->status->label() }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-indigo-400 text-sm">No payments yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>

        <div class="mt-4">{{ $transactions->appends(request()->query())->links() }}</div>

    </div>
</div>
</x-layouts.customer>