@php
    use App\Enums\BookingStatus;
    use App\Enums\ListingStatus;
    use App\Enums\PaymentStatus;
    use App\Enums\PayoutStatus;
    use App\Models\Booking;
    use App\Models\Location;
    use App\Models\Review;
    use App\Models\Transaction;
    use App\Models\User;

    // Unseen activity dots = "something new in this section since the admin
    // last opened it". Opening the page re-stamps the section (see the page
    // controllers), clearing the dot until the next new item arrives.
    // One query loads every stamp for this admin.
    $views = auth()->user()->sectionViews->pluck('viewed_at', 'section');
    $seen = fn (string $section) => $views->get($section);

    $newUsers     = User::when($seen('admin.users'),     fn ($q, $v) => $q->where('created_at', '>', $v))->count();
    $newListings  = Location::where('status', ListingStatus::Pending)
        ->when($seen('admin.listings'),  fn ($q, $v) => $q->where('created_at', '>', $v))->count();
    $newBookings  = Booking::where('status', BookingStatus::Pending)
        ->when($seen('admin.bookings'),  fn ($q, $v) => $q->where('created_at', '>', $v))->count();
    $newPayments  = Transaction::when($seen('admin.payments'),  fn ($q, $v) => $q->where('created_at', '>', $v))->count();
    $newPayouts   = Transaction::where('status', PaymentStatus::Paid)
        ->where('payout_status', PayoutStatus::Unpaid)
        ->whereNotNull('paid_at')
        ->when($seen('admin.payouts'),   fn ($q, $v) => $q->where('paid_at', '>', $v))->count();
    $unviewedReviews = Review::whereNull('admin_reviewed_at')->count();

    $links = [
        ['route' => 'admin.dashboard',  'icon' => 'fa-house',        'label' => 'Dashboard'],
        ['route' => 'admin.users',      'icon' => 'fa-users',        'label' => 'Users',     'unread' => $newUsers],
        ['route' => 'admin.listings',   'icon' => 'fa-location-dot', 'label' => 'Listings',  'unread' => $newListings],
        ['route' => 'admin.categories', 'icon' => 'fa-tags',         'label' => 'Categories'],
        ['route' => 'admin.bookings',   'icon' => 'fa-calendar-days', 'label' => 'Bookings', 'unread' => $newBookings],
        ['route' => 'admin.payments',   'icon' => 'fa-credit-card',  'label' => 'Payments',  'unread' => $newPayments],
        ['route' => 'admin.payouts',    'icon' => 'fa-money-bill-transfer', 'label' => 'Payouts', 'unread' => $newPayouts],
        ['route' => 'admin.reviews',    'icon' => 'fa-star',         'label' => 'Reviews',   'unread' => $unviewedReviews],
        ['route' => 'admin.profile',    'icon' => 'fa-user',         'label' => 'Profile'],
    ];
@endphp

<x-sidebar-nav :links="$links" />
