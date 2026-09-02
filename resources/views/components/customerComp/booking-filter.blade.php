{{-- FILTER TABS --}}
    @php
        $tabs = [
            ''          => 'All',
            'pending'   => 'Pending',
            'confirmed' => 'Confirmed',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'expired'   => 'Expired',
        ];
    @endphp
    <div class="flex flex-wrap gap-2 mb-4">
    @foreach ($tabs as $value => $label)
        <a href="{{ $value === '' ? route('customer.bookings') : route('customer.bookings', ['status' => $value]) }}"
           class="action-btn btn-transition
           {{-- Active state --}}
           {{ request('status', '') === $value
               ? ($value === 'cancelled'
                   ? 'bg-red-600 text-white border border-red-600'
                   : 'bg-indigo-900 text-white border border-indigo-900')
               : ($value === 'cancelled'
                   ? 'bg-white text-red-600 border border-red-400 hover:bg-red-600 hover:text-white'
                   : 'bg-white text-indigo-900 border border-indigo-200 hover:bg-indigo-700 hover:text-white')
           }}">
            {{ $label }}
        </a>
    @endforeach
</div>