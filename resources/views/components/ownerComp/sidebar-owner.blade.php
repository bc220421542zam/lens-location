@php
    $links = [
        ['route' => 'owner.dashboard',        'icon' => 'fa-house',        'label' => 'Dashboard'],
        ['route' => 'owner.bookings',         'icon' => 'fa-calendar',     'label' => 'Bookings'],
        ['route' => 'owner.listings',         'icon' => 'fa-location-dot', 'label' => 'Listings'],
        ['route' => 'owner.locations.create', 'icon' => 'fa-square-plus',  'label' => 'Add Listing'],
        ['route' => 'owner.earnings',         'icon' => 'fa-wallet',       'label' => 'Earnings'],
        ['route' => 'owner.messages',         'icon' => 'fa-message',      'label' => 'Messages'],
        ['route' => 'owner.reviews',          'icon' => 'fa-star',         'label' => 'Reviews'],
        ['route' => 'owner.profile',          'icon' => 'fa-user',         'label' => 'Profile'],
    ];
@endphp

<x-sidebar-nav :links="$links" />
