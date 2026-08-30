@php
    use App\Models\Message;
    use App\Models\Transaction;

    // Unseen activity dots: incoming messages the customer has not opened,
    // unread booking-status notifications (owner confirmed/declined), and
    // payments started since the customer last opened the Payments page.
    // Messages clear when the conversation is opened; booking updates clear
    // when the My Bookings page is opened; new payments clear when the
    // Payments page is opened.
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

    $paymentsSeenAt = auth()->user()->sectionViewedAt('customer.payments');

    $newPayments = Transaction::where('customer_id', auth()->id())
        ->when($paymentsSeenAt, fn ($q, $v) => $q->where('created_at', '>', $v))
        ->count();

    $links = [
        ['route' => 'customer.dashboard', 'icon' => 'fa-house',        'label' => 'Dashboard'],
        ['route' => 'customer.bookings',  'icon' => 'fa-calendar',     'label' => 'My Bookings', 'unread' => $unreadBookingUpdates],
        ['route' => 'customer.listings',  'icon' => 'fa-location-dot', 'label' => 'Search Listings'],
        ['route' => 'customer.messages',  'icon' => 'fa-message',      'label' => 'Messages', 'unread' => $unreadMessages],
        ['route' => 'customer.favorites', 'icon' => 'fa-heart',        'label' => 'Favorites'],
        ['route' => 'customer.payments',  'icon' => 'fa-credit-card',  'label' => 'Payments', 'unread' => $newPayments],
        ['route' => 'customer.profile',   'icon' => 'fa-user',         'label' => 'Profile'],
    ];
@endphp

<x-sidebar-nav :links="$links" />
