{{-- FILTER TABS --}}
    @php
        $tabs = [
            ''          => 'All',
            'pending'   => 'Pending',
            'confirmed' => 'Confirmed',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    @endphp
    <div class="flex flex-wrap gap-2 mb-4">
        @foreach ($tabs as $value => $label)
            <a href="{{ $value === '' ? route('customer.bookings') : route('customer.bookings', ['status' => $value]) }}"
               class="action-btn btn-transition {{ request('status', '') === $value ? 'bg-indigo-900 text-white' : '' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>