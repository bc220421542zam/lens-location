@php
    $links = [
        ['route' => 'customer.dashboard', 'icon' => 'fa-house',        'label' => 'Dashboard'],
        ['route' => 'customer.bookings',  'icon' => 'fa-calendar',     'label' => 'My Bookings'],
        ['route' => 'customer.listings',  'icon' => 'fa-location-dot', 'label' => 'Search Listings'],
        ['route' => 'customer.messages',  'icon' => 'fa-message',      'label' => 'Messages'],
        ['route' => 'customer.favorites', 'icon' => 'fa-heart',        'label' => 'Favorites'],
        ['route' => 'customer.payments',  'icon' => 'fa-credit-card',  'label' => 'Payments'],
        ['route' => 'customer.profile',   'icon' => 'fa-user',         'label' => 'Profile'],
    ];
@endphp

<x-sidebar-nav :links="$links" />
