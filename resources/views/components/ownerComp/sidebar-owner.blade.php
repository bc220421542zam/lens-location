@php
    use App\Enums\BookingStatus;
    use App\Enums\PaymentStatus;
    use App\Models\Booking;
    use App\Models\Location;
    use App\Models\Message;
    use App\Models\Review;
    use App\Models\Transaction;

    // Unseen activity dots = "something new in this section since the owner
    // last opened it". Opening the page re-stamps the section (see the page
    // controllers), clearing the dot until the next new item arrives.
    // Messages clear when the conversation is opened, reviews when the owner
    // opens the Reviews page. One query loads every stamp for this owner.
    $views = auth()->user()->sectionViews->pluck('viewed_at', 'section');
    $seen = fn (string $section) => $views->get($section);

    $ownerLocationIds = Location::select('id')->where('user_id', auth()->id());

    $newBookings = Booking::whereIn('location_id', $ownerLocationIds)
        ->where('status', BookingStatus::Pending)
        ->when($seen('owner.bookings'), fn ($q, $v) => $q->where('created_at', '>', $v))
        ->count();

    $unreadMessages = Message::query()
        ->whereNull('read_at')
        ->where('sender_id', '!=', auth()->id())
        ->whereHas('conversation', fn ($q) => $q
            ->where('customer_id', auth()->id())
            ->orWhere('owner_id', auth()->id()))
        ->count();

    $unviewedReviews = Review::whereIn('location_id', $ownerLocationIds)
        ->where('is_visible', true)
        ->whereNull('owner_reviewed_at')
        ->count();

    $newEarnings = Transaction::where('owner_id', auth()->id())
        ->where('status', PaymentStatus::Paid)
        ->whereNotNull('paid_at')
        ->when($seen('owner.earnings'), fn ($q, $v) => $q->where('paid_at', '>', $v))
        ->count();

    $links = [
        ['route' => 'owner.dashboard',        'icon' => 'fa-house',        'label' => 'Dashboard'],
        ['route' => 'owner.bookings',         'icon' => 'fa-calendar',     'label' => 'Bookings',    'unread' => $newBookings],
        ['route' => 'owner.listings',         'icon' => 'fa-location-dot', 'label' => 'Listings'],
        ['route' => 'owner.locations.create', 'icon' => 'fa-square-plus',  'label' => 'Add Listing'],
        ['route' => 'owner.earnings',         'icon' => 'fa-wallet',       'label' => 'Earnings',    'unread' => $newEarnings],
        ['route' => 'owner.messages',         'icon' => 'fa-message',      'label' => 'Messages',    'unread' => $unreadMessages],
        ['route' => 'owner.reviews',          'icon' => 'fa-star',         'label' => 'Reviews',     'unread' => $unviewedReviews],
        ['route' => 'owner.profile',          'icon' => 'fa-user',         'label' => 'Profile'],
    ];
@endphp

<x-sidebar-nav :links="$links" />
