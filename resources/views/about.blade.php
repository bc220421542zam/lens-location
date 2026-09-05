<x-layout :title="'About Us'">

{{-- ========================= HERO / INTRO ========================= --}}
<section class="relative overflow-hidden bg-linear-to-br from-indigo-900 via-indigo-900 to-indigo-800 text-white">
    <div class="absolute -top-24 -right-24 size-96 rounded-full bg-indigo-500/25 blur-3xl" aria-hidden="true"></div>
    <div class="absolute -bottom-40 -left-24 size-[28rem] rounded-full bg-fuchsia-500/20 blur-3xl" aria-hidden="true"></div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-24 sm:pt-24 sm:pb-32 text-center">
        <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-xs sm:text-sm font-medium text-indigo-100 ring-1 ring-white/20">
            <i class="fa-solid fa-camera" aria-hidden="true"></i>
            About LensLocation
        </p>

        <h1 class="mt-6 text-4xl sm:text-5xl lg:text-6xl font-semibold tracking-tight leading-[1.08]">
            Our mission: make finding the perfect shoot location as easy as pressing the shutter
        </h1>

        <p class="mt-6 max-w-2xl mx-auto text-base sm:text-lg text-indigo-200/90 leading-relaxed">
            LensLocation connects customers with vetted, bookable spaces — and helps location
            owners turn idle properties into steady income. No endless scouting, no awkward
            cold calls. Just search, book, shoot.
        </p>

        <dl class="mt-12 flex flex-wrap items-center justify-center gap-x-10 gap-y-4 border-t border-white/10 pt-8">
            <div>
                <dt class="sr-only">Locations listed</dt>
                <dd class="flex items-baseline gap-2">
                    <span class="text-2xl font-semibold">{{ $stats['locations'] }}</span>
                    <span class="text-xs text-indigo-300">vetted locations</span>
                </dd>
            </div>
            <div>
                <dt class="sr-only">Roles on the platform</dt>
                <dd class="flex items-baseline gap-2">
                    <span class="text-2xl font-semibold">{{ $stats['roles'] }}</span>
                    <span class="text-xs text-indigo-300">roles, one marketplace</span>
                </dd>
            </div>
            <div>
                <dt class="sr-only">Average rating</dt>
                <dd class="flex items-baseline gap-2">
                    <span class="text-2xl font-semibold">{{ $stats['rating'] }}</span>
                    <span class="text-xs text-indigo-300">average rating</span>
                </dd>
            </div>
        </dl>
    </div>
</section>

{{-- ========================= OUR STORY ========================= --}}
<section class="bg-white py-16 sm:py-24" aria-labelledby="story-heading">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-12 lg:grid-cols-2 lg:items-center">
        <div>
            <p class="text-xs sm:text-sm font-semibold uppercase tracking-widest text-indigo-600">Our story</p>
            <h2 id="story-heading" class="mt-3 text-2xl sm:text-3xl lg:text-4xl font-semibold tracking-tight text-indigo-900">
                From a customer's frustration to a two-sided marketplace
            </h2>

            <div class="mt-6 space-y-4 text-gray-600 leading-relaxed">
                <p>
                    LensLocation started with a problem every customer knows: the perfect
                    location usually exists, but finding it — and getting permission to shoot
                    there — takes days of scouting, calls and favours.
                </p>
                <p>
                    On the other side, property owners were sitting on beautiful, under-used
                    spaces with no simple way to rent them out for shoots.
                </p>
                <p>
                    So we built the bridge. Today, LensLocation is a two-sided marketplace where
                    customers browse vetted locations with real photos and transparent
                    pricing, and owners earn from spaces that used to sit empty.
                </p>
                <p>
                    Every listing is reviewed by our team before it goes live, every booking is
                    confirmed by the owner, and every payment is handled securely through Stripe —
                    so both sides can focus on the creative part.
                </p>
            </div>
        </div>

        <figure class="relative mx-auto w-full max-w-md">
            <img src="{{ asset('images/photography.jpg') }}"
                 alt="A customer at work on location"
                 class="w-full aspect-[3/4] rounded-3xl object-cover shadow-xl ring-1 ring-indigo-100">
            <figcaption class="sr-only">Photography on location, made possible by LensLocation</figcaption>

            <div class="absolute -bottom-6 -left-4 sm:-left-8 flex items-center gap-3 rounded-2xl bg-white p-4 pr-5 shadow-xl">
                <span class="size-11 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center" aria-hidden="true">
                    <i class="fa-solid fa-shield-halved"></i>
                </span>
                <span>
                    <span class="block font-semibold text-indigo-900">Vetted spaces only</span>
                    <span class="block text-xs text-gray-500">Every listing is reviewed first</span>
                </span>
            </div>
        </figure>
    </div>
</section>

{{-- ====================== HOW THE ROLES WORK ====================== --}}
<section class="bg-indigo-50/60 py-16 sm:py-24" aria-labelledby="roles-heading">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-section-heading id="roles-heading"
                           eyebrow="Who it's for"
                           title="Three roles, one marketplace"
                           subtitle="LensLocation works because everyone has a clear part to play — and every part makes the others better." />

        <div class="mt-14 grid gap-6 md:grid-cols-3">
            <div class="card-transition flex flex-col rounded-3xl bg-white border border-indigo-100 shadow-lg p-7">
                <div class="flex items-center justify-between">
                    <span class="flex size-12 items-center justify-center rounded-xl bg-indigo-900 text-lg text-white shadow-lg shadow-indigo-900/25" aria-hidden="true">
                        <i class="fa-solid fa-store"></i>
                    </span>
                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-indigo-700">Owners</span>
                </div>
                <h3 class="mt-5 text-lg font-semibold text-indigo-900">Location Owner</h3>
                <p class="mt-2 text-sm text-gray-600 leading-relaxed">
                    Lists spaces with photos, pricing and availability, then approves every booking request.
                </p>
                <ul class="mt-4 space-y-2 text-sm text-gray-600">
                    <li class="flex gap-2"><i class="fa-solid fa-check text-indigo-600 mt-0.5 shrink-0" aria-hidden="true"></i>List spaces with photos &amp; hourly rates</li>
                    <li class="flex gap-2"><i class="fa-solid fa-check text-indigo-600 mt-0.5 shrink-0" aria-hidden="true"></i>Approve or decline every booking</li>
                    <li class="flex gap-2"><i class="fa-solid fa-check text-indigo-600 mt-0.5 shrink-0" aria-hidden="true"></i>Track earnings &amp; Stripe payouts</li>
                </ul>
            </div>

            <div class="card-transition flex flex-col rounded-3xl bg-white border border-indigo-100 shadow-lg p-7">
                <div class="flex items-center justify-between">
                    <span class="flex size-12 items-center justify-center rounded-xl bg-indigo-900 text-lg text-white shadow-lg shadow-indigo-900/25" aria-hidden="true">
                        <i class="fa-solid fa-camera"></i>
                    </span>
                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-indigo-700">Customers</span>
                </div>
                <h3 class="mt-5 text-lg font-semibold text-indigo-900">Customer</h3>
                <p class="mt-2 text-sm text-gray-600 leading-relaxed">
                    Searches vetted spaces by city, category and budget, then books and pays securely.
                </p>
                <ul class="mt-4 space-y-2 text-sm text-gray-600">
                    <li class="flex gap-2"><i class="fa-solid fa-check text-indigo-600 mt-0.5 shrink-0" aria-hidden="true"></i>Search with real filters, not guesswork</li>
                    <li class="flex gap-2"><i class="fa-solid fa-check text-indigo-600 mt-0.5 shrink-0" aria-hidden="true"></i>Favourite spaces &amp; message owners</li>
                    <li class="flex gap-2"><i class="fa-solid fa-check text-indigo-600 mt-0.5 shrink-0" aria-hidden="true"></i>Review every shoot to help the community</li>
                </ul>
            </div>

            <div class="card-transition flex flex-col rounded-3xl bg-white border border-indigo-100 shadow-lg p-7">
                <div class="flex items-center justify-between">
                    <span class="flex size-12 items-center justify-center rounded-xl bg-indigo-900 text-lg text-white shadow-lg shadow-indigo-900/25" aria-hidden="true">
                        <i class="fa-solid fa-user-tie"></i>
                    </span>
                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-indigo-700">Admins</span>
                </div>
                <h3 class="mt-5 text-lg font-semibold text-indigo-900">Admin</h3>
                <p class="mt-2 text-sm text-gray-600 leading-relaxed">
                    The guardian of the marketplace — keeps every listing, review and payout honest.
                </p>
                <ul class="mt-4 space-y-2 text-sm text-gray-600">
                    <li class="flex gap-2"><i class="fa-solid fa-check text-indigo-600 mt-0.5 shrink-0" aria-hidden="true"></i>Reviews listings before they go live</li>
                    <li class="flex gap-2"><i class="fa-solid fa-check text-indigo-600 mt-0.5 shrink-0" aria-hidden="true"></i>Moderates reviews &amp; user accounts</li>
                    <li class="flex gap-2"><i class="fa-solid fa-check text-indigo-600 mt-0.5 shrink-0" aria-hidden="true"></i>Oversees bookings &amp; payouts</li>
                </ul>
            </div>
        </div>

        {{-- How the roles connect --}}
        <h3 class="mt-16 text-center text-lg sm:text-xl font-semibold text-indigo-900">How they connect</h3>
        <ol class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['title' => 'Owners list spaces', 'text' => 'With real photos, hourly pricing and availability.'],
                ['title' => 'Admins vet listings', 'text' => 'Every space is reviewed before it goes live.'],
                ['title' => 'Customers book', 'text' => 'Search, book and pay securely in minutes.'],
                ['title' => 'Everyone reviews', 'text' => 'Ratings keep the marketplace honest for all.'],
            ] as $step)
                <li class="relative card-transition rounded-3xl border border-indigo-100 bg-[#EEEFF7]/70 p-6 pt-8 text-center">
                    <span class="inline-flex items-center rounded-full bg-indigo-900 px-3 py-1 text-[11px] font-semibold uppercase tracking-widest text-white">Step {{ $loop->iteration }}</span>
                    <h4 class="mt-4 font-semibold text-indigo-900">{{ $step['title'] }}</h4>
                    <p class="mt-2 text-sm text-gray-600 leading-relaxed">{{ $step['text'] }}</p>

                    @unless ($loop->last)
                        <span class="absolute top-1/2 -right-6 z-10 hidden lg:flex size-9 -translate-y-1/2 items-center justify-center rounded-full bg-white border border-indigo-200 text-indigo-400 shadow-sm" aria-hidden="true">
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </span>
                    @endunless
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- ========================= VALUES ========================= --}}
<section class="bg-white py-16 sm:py-24" aria-labelledby="values-heading">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-section-heading id="values-heading"
                           eyebrow="What we stand for"
                           title="The values behind every listing, booking and payout"
                           subtitle="Small promises we make to everyone who uses the platform." />

        <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <div class="card-transition rounded-3xl border border-indigo-100 bg-[#EEEFF7]/70 p-7 text-center">
                <span class="mx-auto flex size-12 items-center justify-center rounded-xl bg-indigo-100 text-xl text-indigo-800" aria-hidden="true">
                    <i class="fa-solid fa-shield-halved"></i>
                </span>
                <h3 class="mt-4 font-semibold text-indigo-900">Trust &amp; safety</h3>
                <p class="mt-2 text-sm text-gray-600 leading-relaxed">Every listing is vetted and every payment is protected.</p>
            </div>

            <div class="card-transition rounded-3xl border border-indigo-100 bg-[#EEEFF7]/70 p-7 text-center">
                <span class="mx-auto flex size-12 items-center justify-center rounded-xl bg-indigo-100 text-xl text-indigo-800" aria-hidden="true">
                    <i class="fa-solid fa-scale-balanced"></i>
                </span>
                <h3 class="mt-4 font-semibold text-indigo-900">Fair &amp; transparent</h3>
                <p class="mt-2 text-sm text-gray-600 leading-relaxed">Owners set their rates; customers always see the full price upfront.</p>
            </div>

            <div class="card-transition rounded-3xl border border-indigo-100 bg-[#EEEFF7]/70 p-7 text-center">
                <span class="mx-auto flex size-12 items-center justify-center rounded-xl bg-indigo-100 text-xl text-indigo-800" aria-hidden="true">
                    <i class="fa-solid fa-users"></i>
                </span>
                <h3 class="mt-4 font-semibold text-indigo-900">Community first</h3>
                <p class="mt-2 text-sm text-gray-600 leading-relaxed">Real reviews from real shoots keep quality high for everyone.</p>
            </div>

            <div class="card-transition rounded-3xl border border-indigo-100 bg-[#EEEFF7]/70 p-7 text-center">
                <span class="mx-auto flex size-12 items-center justify-center rounded-xl bg-indigo-100 text-xl text-indigo-800" aria-hidden="true">
                    <i class="fa-solid fa-award"></i>
                </span>
                <h3 class="mt-4 font-semibold text-indigo-900">Quality over quantity</h3>
                <p class="mt-2 text-sm text-gray-600 leading-relaxed">We'd rather feature ten great spaces than a hundred average ones.</p>
            </div>
        </div>
    </div>
</section>

{{-- ========================= CLOSING CTA ========================= --}}
<section class="bg-[#EEEFF7] py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative chart-transition overflow-hidden rounded-[2rem] bg-linear-to-r from-indigo-900 to-indigo-700 px-6 py-14 sm:px-14 sm:py-16 text-center text-white shadow-xl">
            <div class="absolute -top-24 -left-24 size-72 rounded-full bg-white/10 blur-3xl" aria-hidden="true"></div>
            <div class="absolute -bottom-28 -right-20 size-80 rounded-full bg-fuchsia-400/20 blur-3xl" aria-hidden="true"></div>

            <div class="relative max-w-2xl mx-auto">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-semibold tracking-tight">Join the LensLocation community</h2>
                <p class="mt-4 text-indigo-100 leading-relaxed">
                    Whether you're booking your next shoot or listing your first space,
                    your account is free and takes a minute to set up.
                </p>

                <div class="mt-9 flex flex-col sm:flex-row items-center justify-center gap-3">
                    @guest
                        <a href="{{ route('register') }}"
                           class="w-full sm:w-auto px-7 py-3 rounded-xl bg-white text-indigo-900 font-medium shadow-sm hover:bg-indigo-50 focus-visible:ring-2 focus-visible:ring-white transition-colors duration-200">
                            Get started — it's free
                        </a>
                    @else
                        <a href="{{ auth()->user()->role === \App\Enums\Role::Customer ? route('customer.listings') : route(auth()->user()->role->dashboardRoute()) }}"
                           class="w-full sm:w-auto px-7 py-3 rounded-xl bg-white text-indigo-900 font-medium shadow-sm hover:bg-indigo-50 focus-visible:ring-2 focus-visible:ring-white transition-colors duration-200">
                            Browse locations
                        </a>
                    @endguest

                    <a href="{{ route('home') }}"
                       class="w-full sm:w-auto px-7 py-3 rounded-xl border border-white/40 text-white font-medium hover:bg-white/10 focus-visible:ring-2 focus-visible:ring-white transition-colors duration-200">
                        See how it works
                    </a>
                </div>

                <p class="mt-6 text-xs text-indigo-300">Free to join · No credit card required</p>
            </div>
        </div>
    </div>
</section>
</x-layout>
