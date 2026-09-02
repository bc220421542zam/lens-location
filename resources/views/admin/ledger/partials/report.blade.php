{{-- Period-totals card. Re-rendered server-side on every AJAX fetch with the
     request's query, so the Export PDF link always matches the visible
     filters. The id keeps the fragment addressable across outerHTML swaps. --}}
<div id="ledger-report" class="flex flex-col shade card chart-transition bg-[#EEEFF7] p-4 rounded-2xl border-l-3 border-indigo-400">
    <h3 class="font-bold text-indigo-900 text-lg">Period totals</h3>
    <p class="mt-1 text-sm text-gray-500">{{ $period === 'weekly' ? 'Weekly breakdown for '.now()->format('F Y') : 'Monthly breakdown for the last six months' }}</p>

    <div class="mt-4 bg-white rounded-xl shadow-sm border border-r-3 border-indigo-400 overflow-hidden">
        <table class="table-clean w-full text-left text-sm">
            <thead>
                <tr class="text-[11px] uppercase tracking-wide text-indigo-900 border-b border-indigo-100 bg-indigo-50/60">
                    <th class="py-3 px-3 font-medium">Period</th>
                    <th class="px-3 font-medium text-right">Collected</th>
                    <th class="px-3 font-medium text-right">Commission</th>
                    <th class="px-3 font-medium text-right">Owner payout</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($trend as $bucket)
                    <tr class="border-t border-indigo-50 {{ $loop->even ? 'bg-indigo-50/40' : 'bg-white' }} hover:bg-indigo-50 transition-colors">
                        <td class="py-2.5 px-3 font-medium text-indigo-900">{{ $bucket['label'] }}</td>
                        <td class="px-3 tabular-nums text-indigo-700 text-right">Rs {{ number_format($bucket['collected']) }}</td>
                        <td class="px-3 tabular-nums text-yellow-600 text-right">Rs {{ number_format($bucket['commission']) }}</td>
                        <td class="px-3 tabular-nums font-medium text-green-700 text-right">Rs {{ number_format($bucket['payout']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-auto flex justify-end pt-4">
        <a href="{{ route('admin.ledger.export', request()->query()) }}"
           class="inline-flex items-center gap-2 btn-transition rounded-lg bg-indigo-900 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-800">
            <i class="fa-regular fa-file-pdf" aria-hidden="true"></i> Export PDF
        </a>
    </div>
</div>
