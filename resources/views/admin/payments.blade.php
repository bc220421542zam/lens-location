<x-layouts.admin>
<div x-data="{
    showModal: false,
    selectedTxn: null,
    openTxn(txn) {
        this.selectedTxn = txn;
        this.showModal = true;
    }
}">
    {{-- Top bar --}}
    <x-topbar
        title="Payments"
        description="All platform transactions">
    </x-topbar>

    {{-- Stats --}}
    <x-adminComponents.payment-stats :stats="$stats" />

    {{--Filters --}}
    <x-adminComponents.payment-filter/>
    
    {{-- Payments Table --}}
    <div class="shade card chart-transition bg-[#EEEFF7] p-4 rounded-2xl border-l-3 border-indigo-400">
        <x-success class="mb-4" />
        <x-error class="mb-4" />

        <div class="flex flex-col gap-3 mb-4">
            <h2 class="font-bold text-indigo-900 text-lg">All Transactions</h2>
        </div>

        <div class="overflow-x-auto">
            <div class="bg-white rounded-xl shadow-sm border border-r-3 border-indigo-400 overflow-hidden">

                <table class="w-full text-left min-w-[900px]">
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

                            <th class="px-2 font-medium">
                                <a href="{{ $sortLink('gateway_ref') }}" class="inline-flex items-center gap-1 hover:text-indigo-600">
                                    Ref
                                    @if($currentSort === 'gateway_ref')
                                        <i class="fa-solid fa-sort-{{ $currentDirection === 'asc' ? 'up' : 'down' }} text-[10px]"></i>
                                    @else
                                        <i class="fa-solid fa-sort text-[10px] text-indigo-300"></i>
                                    @endif
                                </a>
                            </th>

                            <th class="px-2 font-medium">Listing</th>
                            <th class="px-2 font-medium">Customer</th>
                            <th class="px-2 font-medium">Owner</th>

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

                            <th class="px-2 font-medium">Owner Earning</th>
                            <th class="px-2 font-medium">Commission</th>

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

                            <th class="px-2 font-medium text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $t)
                        <tr class="border-t border-indigo-50 {{ $loop->even ? 'bg-indigo-50/40' : 'bg-white' }} hover:bg-indigo-50 transition-colors">
                            <td class="py-3 px-4 text-sm text-indigo-700">
                                {{ $transactions->firstItem() + $loop->index }}
                            </td>
                            <td class="px-2 text-sm text-indigo-700">{{ $t->reference() }}</td>
                            <td class="px-2 text-sm font-medium text-indigo-900">{{ $t->booking->location->title ?? '—' }}</td>
                            <td class="px-2 text-sm text-indigo-700">{{ $t->customer->first_name ?? '—' }}</td>
                            <td class="px-2 text-sm text-indigo-700">{{ $t->owner->first_name ?? '—' }}</td>
                            <td class="px-2 text-sm text-indigo-700">Rs. {{ number_format($t->amount, 2) }}</td>
                            <td class="px-2 text-sm text-indigo-700">Rs. {{ number_format($t->owner_earning, 2) }}</td>
                            <td class="px-2 text-sm text-indigo-700">Rs. {{ number_format($t->platform_fee, 2) }}</td>
                            <td class="px-2 text-sm">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $t->status->badgeClasses() }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $t->status->dotClasses() }}"></span>
                                    {{ $t->status->label() }}
                                </span>
                            </td>
                            <td class="px-2 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="openTxn({{ $t->toJson() }})"
                                        title="View"
                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">
                                        <i class="fa-regular fa-eye text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="py-6 text-center text-indigo-400 text-sm">No transactions found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>

        <div class="mt-4">{{ $transactions->appends(request()->query())->links() }}</div>

    </div>

    {{-- MODAL --}}
    <div x-show="showModal"
        class="fixed inset-0 z-50 flex items-center justify-center px-4"
        style="display:none">

        <div class="absolute inset-0 bg-black opacity-50" @click="showModal = false"></div>

        <div class="relative card card-transition bg-white rounded-2xl shadow-xl p-6 w-full max-w-md z-10 max-h-[90vh] overflow-y-auto">

            <button @click="showModal = false"
                class="absolute btn-trnasition top-3 right-4 text-indigo-900 text-xl hover:opacity-75">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <h2 class="text-lg font-bold text-indigo-900 mb-4">Transaction Detail</h2>

            <template x-if="selectedTxn">
                <div class="space-y-2">

                    <div class="flex justify-between border-b border-indigo-100 pb-2">
                        <span class="text-indigo-900 text-sm">Ref</span>
                        <span class="text-indigo-900 text-sm font-medium break-all ml-4" x-text="selectedTxn.stripe_payment_intent_id || selectedTxn.gateway_ref || '—'"></span>
                    </div>

                    <div class="flex justify-between border-b border-indigo-100 pb-2">
                        <span class="text-indigo-900 text-sm">Amount</span>
                        <span class="text-indigo-900 text-sm font-medium" x-text="'Rs. ' + selectedTxn.amount"></span>
                    </div>

                    <div class="flex justify-between border-b border-indigo-100 pb-2">
                        <span class="text-indigo-900 text-sm">Owner Earning</span>
                        <span class="text-indigo-900 text-sm font-medium" x-text="'Rs. ' + selectedTxn.owner_earning"></span>
                    </div>

                    <div class="flex justify-between pb-2">
                        <span class="text-indigo-900 text-sm">Status</span>
                        <span class="text-sm font-medium"
                            :class="selectedTxn.status === 'paid' ? 'text-green-500' : (selectedTxn.status === 'failed' ? 'text-red-500' : (selectedTxn.status === 'refunded' ? 'text-gray-500' : 'text-yellow-500'))"
                            x-text="selectedTxn.status ?? 'N/A'">
                        </span>
                    </div>

                </div>
            </template>

            <div class="mt-5 text-right">
                <button @click="showModal = false"
                    class="bg-indigo-900 btn-transition text-white px-5 py-2 rounded-lg text-sm hover:bg-[#2C3399]">
                    Close
                </button>
            </div>

        </div>
    </div>

</div>
</x-layouts.admin>