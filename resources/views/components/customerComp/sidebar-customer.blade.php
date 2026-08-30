@php
    use App\Models\Message;

    // Unseen activity dots: incoming messages the customer has not opened, and
    // unread booking-status notifications (owner confirmed/declined). Viewing
    // the matching page marks them read, which clears the dot.
    $unreadMessages = Message::query()
        ->whereNull('read_at')
        ->where('sender_id', '!=', auth()->id())
        ->whereHas('conversation', fn ($q) => $q
            ->where('customer_id', auth()->id())
            ->orWhere('owner_id', auth()->id()))
        ->count();

    $unreadBookingUpdates = auth()->user()->unreadNotifications()
        ->where('data->type', 'booking_status')
        ->count();

    $links = [
        ['route' => 'customer.dashboard', 'icon' => 'fa-house',        'label' => 'Dashboard'],
        ['route' => 'customer.bookings',  'icon' => 'fa-calendar',     'label' => 'My Bookings', 'unread' => $unreadBookingUpdates],
        ['route' => 'customer.listings',  'icon' => 'fa-location-dot', 'label' => 'Search Listings'],
        ['route' => 'customer.messages',  'icon' => 'fa-message',      'label' => 'Messages', 'unread' => $unreadMessages],
        ['route' => 'customer.favorites', 'icon' => 'fa-heart',        'label' => 'Favorites'],
        ['route' => 'customer.payments',  'icon' => 'fa-credit-card',  'label' => 'Payments'],
        ['route' => 'customer.profile',   'icon' => 'fa-user',         'label' => 'Profile'],
    ];
@endphp

<x-sidebar-nav :links="$links" />
