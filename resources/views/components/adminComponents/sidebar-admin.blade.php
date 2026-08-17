@php
    $links = [
        ['route' => 'admin.dashboard',  'icon' => 'fa-house',        'label' => 'Dashboard'],
        ['route' => 'admin.users',      'icon' => 'fa-users',        'label' => 'Users'],
        ['route' => 'admin.listings',   'icon' => 'fa-location-dot', 'label' => 'Listings'],
        ['route' => 'admin.categories', 'icon' => 'fa-tags',         'label' => 'Categories'],
        ['route' => 'admin.payments',   'icon' => 'fa-credit-card',  'label' => 'Payments'],
        ['route' => 'admin.reviews',    'icon' => 'fa-star',         'label' => 'Reviews'],
        ['route' => 'admin.profile',    'icon' => 'fa-user',         'label' => 'Profile'],
    ];
@endphp

<x-sidebar-nav :links="$links" />
