<x-layouts.admin>
<div>
    {{-- Top bar --}}
    <x-topbar
        title="Payouts"
        description="Owner payouts for paid transactions">
    </x-topbar>

    <x-success class="mb-4" />
    <x-error class="mb-4" />

    {{-- Stats --}}
    <x-adminComponents.payout-stats :stats="$stats" />

    {{-- Filters --}}
    @if ($errors->any())
        <div class="mb-3 rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="GET" action="{{ route('admin.payouts') }}"
          class="flex flex-col md:flex-row md:items-end gap-3 card chart-transition mb-4 justify-between rounded-2xl border-l-3 border-indigo-400">

        {{-- Search --}}
        <div class="flex-1">
            <label for="filter-search" class="flex items-center gap-1.5 text-indigo-900 mb-1 text-sm">
                <svg class="w-3.5 h-3.5 opacity-70" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    d="M17 17l-3.5-3.5M13 8.5a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                </svg>
                Search
            </label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by owner, transfer id ..."
                   class="input text-indigo-800">
        </div>

        {{-- Payout status --}}
        <div class="w-full md:w-48">
            <label for="filter-payout-status" class="flex items-center gap-1.5 text-indigo-900 mb-1 text-sm">
                <svg class="w-3.5 h-3.5 opacity-70" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    d="M10 5v5l3 3M10 3a7 7 0 100 14A7 7 0 0010 3z"/>
                </svg>
                Payout
            </label>
            <div class="relative">
                <select name="payout_status" id="filter-payout-status" class="input text-indigo-800 w-full appearance-none pr-8">
                    <option value="">All</option>
                    <option value="unpaid" @selected(request('payout_status') === 'unpaid')>In transit</option>
                    <option value="paid"    @selected(request('payout_status') === 'paid')>Transferred</option>
                </select>
                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                    <svg class="w-3.5 h-3.5 text-indigo-800" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Buttons --}}
        <div class="flex gap-2 items-end">
            <button type="submit" class="btn w-auto px-4 mt-5 rounded-lg inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 opacity-70" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                    d="M3 5h14M6 10h8M9 15h2"/>
                </svg>
                Filter
            </button>
        </div>

        <div class="flex items-end">
            @if (request()->hasAny(['search', 'payout_status']))
                <a href="{{ route('admin.payouts') }}"
                   class="px-4 w-auto mt-5 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 opacity-70" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                        d="M5 5l10 10M15 5L5 15"/>
                    </svg>
                    Reset
                </a>
            @endif
        </div>
    </form>

    {{-- Payouts Ledger --}}
    <div class="shade card chart-transition bg-[#EEEFF7] p-4 rounded-2xl border-l-3 border-indigo-400">
        <div class="flex flex-col gap-3 mb-4">
            <h2 class="font-bold text-indigo-900 text-lg">Payout Ledger</h2>
        </div>

        <div class="overflow-x-auto">
            <div class="bg-white rounded-xl shadow-sm border border-r-3 border-indigo-400 overflow-hidden">

                <table class="w-full text-left min-w-[900px]">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wide text-indigo-900 border-b border-indigo-100 bg-indigo-50/60">
                            <th class="py-3 px-3 font-medium">Sr. No.</th>
                            <th class="px-2 font-medium">Booking</th>
                            <th class="px-2 font-medium">Owner</th>
                            <th class="px-2 font-medium">Owner Earning</th>
                            <th class="px-2 font-medium">Payout</th>
                            <th class="px-2 font-medium">Transfer Id</th>
                            <th class="px-2 font-medium">Paid At</th>
                            <th class="px-2 font-medium text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $t)
                        <tr class="border-t border-indigo-50 {{ $loop->even ? 'bg-indigo-50/40' : 'bg-white' }} hover:bg-indigo-50 transition-colors">
                            <td class="py-3 px-4 text-sm text-indigo-700">
                                {{ $transactions->firstItem() + $loop->index }}
                            </td>
                            <td class="px-2 text-sm text-indigo-700">#{{ $t->booking_id }}</td>
                            <td class="px-2 text-sm text-indigo-700">
                                @if($t->owner)
                                    <a href="{{ route('admin.users.detail', $t->owner->id) }}"
                                       class="hover:text-indigo-900 hover:underline">{{ $t->owner->name }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-2 text-sm text-indigo-700">Rs. {{ number_format($t->owner_earning, 2) }}</td>
                            <td class="px-2 text-sm">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $t->payout_status->badgeClasses() }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $t->payout_status->dotClasses() }}"></span>
                                    {{ $t->payout_status->label() }}
                                </span>
                            </td>
                            <td class="px-2 text-sm text-indigo-700">{{ $t->stripe_transfer_id ?? '—' }}</td>
                            <td class="px-2 text-sm text-indigo-700">{{ $t->paid_at?->format('d M Y') ?? '—' }}</td>
                            <td class="px-2 py-3">
                                @if($t->payout_status === \App\Enums\PayoutStatus::Unpaid)
                                    <form method="POST" action="{{ route('admin.payouts.mark', $t->id) }}"
                                          class="flex flex-col items-end gap-1">
                                        @csrf
                                        <input type="text" name="reference" placeholder="transfer id (optional)"
                                               class="input text-indigo-800 text-xs py-1 px-2 w-44">
                                        <button type="submit"
                                                class="text-xs px-3 py-1.5 rounded-lg bg-indigo-900 text-white hover:bg-[#2C3399] transition">
                                            Mark transferred
                                        </button>
                                    </form>
                                @else
                                    <span class="inline-flex items-center gap-1 text-green-600 text-sm">
                                        <i class="fa-solid fa-circle-check"></i> Paid
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-6 text-center text-indigo-400 text-sm">No payouts found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>

        <div class="mt-4">{{ $transactions->appends(request()->query())->links() }}</div>

    </div>
</div>
</x-layouts.admin>
