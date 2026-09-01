<x-layout :title="'Find & book your next shoot location'">
@php
    /**
     * Locations. HomeController always passes approved listings in
     * $locations — the nine featured ones, narrowed down when the hero
     * search was submitted ($isSearch). The section below swaps between
     * the two states on this same page.
     */
    $testimonials = [
        ['quote' => 'I booked a rooftop studio for a bridal shoot in under ten minutes. The photos came out incredible and the owner was so helpful.',
         'name' => 'Ayesha Khan', 'role' => 'Wedding Customer', 'initials' => 'AK'],
        ['quote' => 'Listing my studio on LensLocation pays for itself. I get steady bookings and the dashboard makes managing everything effortless.',
         'name' => 'Bilal Ahmed', 'role' => 'Studio Owner, Lahore', 'initials' => 'BA'],
        ['quote' => 'As a commercial shooter I need locations fast. The filters make finding the right space in the right city a two-minute job.',
         'name' => 'Sarah Malik', 'role' => 'Commercial Customer', 'initials' => 'SM'],
    ];
@endphp

{{-- ============================ HERO ============================ --}}
<section class="relative overflow-hidden bg-linear-to-br from-indigo-950 via-indigo-900 to-indigo-800 text-white">
    <div class="absolute -top-24 -left-24 size-96 rounded-full bg-indigo-500/25 blur-3xl" aria-hidden="true"></div>
    <div class="absolute -bottom-40 -right-24 size-[28rem] rounded-full bg-fuchsia-500/20 blur-3xl" aria-hidden="true"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-24 sm:pt-20 sm:pb-32 lg:grid lg:grid-cols-12 lg:gap-12 lg:items-center">
        <div class="lg:col-span-6">
            <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-xs sm:text-sm font-medium text-indigo-100 ring-1 ring-white/20">
                <i class="fa-solid fa-camera" aria-hidden="true"></i>
                The marketplace for shoot locations
            </p>

            <h1 class="mt-6 text-4xl sm:text-5xl lg:text-6xl font-semibold tracking-tight leading-[1.08]">
                Find the perfect location for your next shoot
            </h1>

            <p class="mt-5 max-w-xl text-base sm:text-lg text-indigo-200/90 leading-relaxed">
                Browse studios, rooftops and hidden gems across Pakistan. Book in minutes,
                pay securely, and shoot with confidence.
            </p>

            {{-- Search: submits to this same page — results swap in for the
                 locations section below (search, category) — no login needed --}}
            <form action="{{ route('home') }}" method="GET" role="search" class="mt-8 max-w-xl">
                <div class="flex flex-col md:flex-row gap-2 rounded-2xl bg-white p-2 shadow-2xl shadow-indigo-950/40 ring-1 ring-white/20">
                    <label class="sr-only" for="hero-search">Search locations by city or title</label>
                    <div class="flex flex-1 items-center gap-2.5 rounded-xl px-3.5 transition-colors duration-200 focus-within:bg-indigo-50">
                        <i class="fa-solid fa-magnifying-glass text-indigo-500" aria-hidden="true"></i>
                        <input id="hero-search" name="search" type="search" value="{{ request('search') }}"
                               placeholder="Search by city, e.g. Karachi"
                               class="w-full bg-transparent py-3 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none">
                    </div>

                    <label class="sr-only" for="hero-category">Filter by category</label>
                    <select id="hero-category" name="category"
                            class="rounded-xl border-0 bg-indigo-50 px-3.5 py-3 text-sm font-medium text-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">All categories</option>
                        @foreach ($categories ?? [] as $category)
                            <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                        @endforeach
                    </select>

                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-900 px-6 py-3 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 transition-colors duration-200">
                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i> Search
                    </button>
                </div>
            </form>

            <p class="mt-4 text-xs text-indigo-300">
                Popular:
                @foreach (['Karachi', 'Lahore', 'Studio', 'Rooftop'] as $term)
                    <a href="{{ route('home', ['search' => $term]) }}"
                       class="font-medium text-indigo-200 underline-offset-4 hover:text-white hover:underline transition-colors duration-200">{{ $term }}</a>{{ ! $loop->last ? ' · ' : '' }}
                @endforeach
            </p>

            <dl class="mt-10 flex flex-wrap items-center gap-x-10 gap-y-4 border-t border-white/10 pt-6">
                <div>
                    <dt class="sr-only">Locations listed</dt>
                    <dd class="flex items-baseline gap-2">
                        <span class="text-2xl font-semibold">500+</span>
                        <span class="text-xs text-indigo-300">locations</span>
                    </dd>
                </div>
                <div>
                    <dt class="sr-only">Shoots booked</dt>
                    <dd class="flex items-baseline gap-2">
                        <span class="text-2xl font-semibold">12,000+</span>
                        <span class="text-xs text-indigo-300">shoots booked</span>
                    </dd>
                </div>
                <div>
                    <dt class="sr-only">Average rating</dt>
                    <dd class="flex items-baseline gap-2">
                        <span class="text-2xl font-semibold">4.9★</span>
                        <span class="text-xs text-indigo-300">average rating</span>
                    </dd>
                </div>
            </dl>
        </div>

        <div class="relative hidden md:block lg:col-span-6 mt-16 lg:mt-0">
            <div class="absolute -inset-6 rounded-[2rem] bg-linear-to-tr from-indigo-500/20 to-fuchsia-500/10 blur-2xl" aria-hidden="true"></div>

            @if (file_exists(public_path('videos/hero-shoot.mp4')))
                {{-- Looping hero video of a shoot at a listed location. The photo
                     doubles as the poster, so the hero looks right while the
                     video loads — and if the file is ever removed, the @else
                     branch below renders the image instead. --}}
                <video autoplay muted loop playsinline
                       poster="{{ asset('images/Studio-photography-rental.jpg') }}"
                       aria-hidden="true"
                       class="relative w-full aspect-[4/3] rounded-3xl object-cover shadow-2xl ring-1 ring-white/20">
                    <source src="{{ asset('videos/hero-shoot.mp4') }}" type="video/mp4">
                </video>
            @else
                <img src="{{ asset('images/Studio-photography-rental.jpg') }}"
                     alt="A customer shooting inside a rental studio"
                     class="relative w-full aspect-[4/3] rounded-3xl object-cover shadow-2xl ring-1 ring-white/20">
            @endif

            <div class="absolute -bottom-6 -left-4 sm:-left-8 flex items-center gap-3 rounded-2xl bg-white p-4 pr-5 shadow-xl">
                <span class="size-11 rounded-xl bg-amber-100 text-amber-500 flex items-center justify-center" aria-hidden="true">
                    <i class="fa-solid fa-star"></i>
                </span>
                <span>
                    <span class="block font-semibold text-indigo-950">4.9 / 5</span>
                    <span class="block text-xs text-gray-500">2,300+ verified reviews</span>
                </span>
            </div>

            <div class="absolute -top-5 -right-4 sm:-right-6 flex items-center gap-3 rounded-2xl bg-white p-4 pr-5 shadow-xl">
                <span class="size-11 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center" aria-hidden="true">
                    <i class="fa-solid fa-bolt"></i>
                </span>
                <span>
                    <span class="block font-semibold text-indigo-950">Instant booking</span>
                    <span class="block text-xs text-gray-500">Owners confirm in minutes</span>
                </span>
            </div>
        </div>
    </div>
</section>

{{-- ======================== HOW IT WORKS ======================== --}}
<section class="bg-white py-16 sm:py-24" aria-labelledby="how-it-works-heading">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-section-heading id="how-it-works-heading"
                           eyebrow="How it works"
                           title="From search to shoot in three steps"
                           subtitle="No cold calls, no endless scouting — just a clean path from idea to photoshoot." />

        <ol class="mt-14 grid gap-6 lg:grid-cols-3">
            <li class="card-transition rounded-3xl border border-indigo-100 bg-[#EEEFF7]/70 p-8 text-center">
                <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-widest text-indigo-800">Step 1</span>
                <span class="mx-auto mt-6 flex size-16 items-center justify-center rounded-2xl bg-indigo-900 text-2xl text-white shadow-lg shadow-indigo-900/25" aria-hidden="true">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <h3 class="mt-5 text-xl font-semibold text-indigo-950">Search</h3>
                <p class="mt-3 text-sm text-gray-600 leading-relaxed">Filter by city, category or budget and find a space that matches your vision.</p>
            </li>

            <li class="card-transition rounded-3xl border border-indigo-100 bg-[#EEEFF7]/70 p-8 text-center">
                <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-widest text-indigo-800">Step 2</span>
                <span class="mx-auto mt-6 flex size-16 items-center justify-center rounded-2xl bg-indigo-900 text-2xl text-white shadow-lg shadow-indigo-900/25" aria-hidden="true">
                    <i class="fa-solid fa-calendar-check"></i>
                </span>
                <h3 class="mt-5 text-xl font-semibold text-indigo-950">Book</h3>
                <p class="mt-3 text-sm text-gray-600 leading-relaxed">Send a booking request, agree the time and price, and pay securely online.</p>
            </li>

            <li class="card-transition rounded-3xl border border-indigo-100 bg-[#EEEFF7]/70 p-8 text-center">
                <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-widest text-indigo-800">Step 3</span>
                <span class="mx-auto mt-6 flex size-16 items-center justify-center rounded-2xl bg-indigo-900 text-2xl text-white shadow-lg shadow-indigo-900/25" aria-hidden="true">
                    <i class="fa-solid fa-camera"></i>
                </span>
                <h3 class="mt-5 text-xl font-semibold text-indigo-950">Shoot</h3>
                <p class="mt-3 text-sm text-gray-600 leading-relaxed">Show up, set up, and create — with the confidence the space is yours for the day.</p>
            </li>
        </ol>
    </div>
</section>

{{-- ================== LOCATIONS / SEARCH RESULTS ================== --}}
{{-- Always visible. Without a query it shows the featured six; a search from
     the hero swaps this section for the matching results on the same page.
     Guest cards are shallow previews (image, title, city) pointing at login. --}}
<section class="bg-indigo-50/60 py-16 sm:py-24" aria-labelledby="locations-heading">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                @if ($isSearch)
                    <p class="text-xs sm:text-sm font-semibold uppercase tracking-widest text-indigo-600">Search results</p>
                    <h2 id="locations-heading" class="mt-3 text-2xl sm:text-3xl lg:text-4xl font-semibold tracking-tight text-indigo-950">Locations matching your search</h2>
                    <p class="mt-3 text-gray-600">
                        {{ $locations->count() }} {{ Str::plural('location', $locations->count()) }}
                        @guest
                            &middot; Log in to see prices, availability and full details.
                        @endguest
                    </p>
                @else
                    <p class="text-xs sm:text-sm font-semibold uppercase tracking-widest text-indigo-600">Featured spaces</p>
                    <h2 id="locations-heading" class="mt-3 text-2xl sm:text-3xl lg:text-4xl font-semibold tracking-tight text-indigo-950">Locations our customers love</h2>
                    <p class="mt-3 text-gray-600">Hand-picked spaces the community keeps coming back to.</p>
                @endif
            </div>

            @if ($isSearch)
                <a href="{{ route('home') }}"
                   class="inline-flex shrink-0 items-center gap-2 self-start sm:self-auto rounded-xl border border-indigo-900 px-5 py-2.5 text-sm font-medium text-indigo-900 hover:bg-indigo-50 transition-colors duration-200">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i> Clear search
                </a>
            @elseif (auth()->check() && auth()->user()->role === \App\Enums\Role::Customer)
                <a href="{{ route('customer.listings') }}"
                   class="inline-flex shrink-0 items-center gap-2 self-start sm:self-auto rounded-xl border border-indigo-900 px-5 py-2.5 text-sm font-medium text-indigo-900 hover:bg-indigo-50 transition-colors duration-200">
                    Browse all locations <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>
            @endif
        </div>

        @if ($locations->isEmpty())
            <div class="mt-12 rounded-3xl border border-indigo-100 bg-white p-12 text-center text-gray-500">
                <i class="fa-solid fa-magnifying-glass text-3xl text-indigo-200 mb-3" aria-hidden="true"></i>
                <p class="font-medium">{{ $isSearch ? 'No locations match your search' : 'No locations are live yet — check back soon!' }}</p>
                @if ($isSearch)
                    <a href="{{ route('home') }}" class="mt-4 inline-block text-sm font-medium text-indigo-700 hover:underline">
                        Clear the search
                    </a>
                @endif
            </div>
        @else
            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($locations as $location)
                    @guest
                        <x-location-preview-card :title="$location->title"
                                                 :image="$location->image ? Storage::url($location->image) : null"
                                                 :city="$location->city"
                                                 :href="route('login')" />
                    @else
                        @php
                            $cardRating = $location->reviews_avg_rating ? round((float) $location->reviews_avg_rating, 1) : null;

                            $cardHref = match (auth()->user()->role) {
                                \App\Enums\Role::Customer => route('customer.listings.show', $location->id),
                                \App\Enums\Role::Owner    => route('owner.dashboard'),
                                \App\Enums\Role::Admin    => route('admin.dashboard'),
                                default                   => route('login'),
                            };
                        @endphp
                        <x-location-card :title="$location->title"
                                         :image="$location->image ? Storage::url($location->image) : null"
                                         :city="$location->city"
                                         :category="$location->category"
                                         :price="$location->price_per_hour"
                                         :rating="$cardRating"
                                         :href="$cardHref" />
                    @endguest
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ======================= WHY LENSLOCATION ===================== --}}
<section class="bg-white py-16 sm:py-24" aria-labelledby="why-heading">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-section-heading id="why-heading"
                           eyebrow="Why LensLocation"
                           title="Built for both sides of the camera"
                           subtitle="One marketplace where customers find their dream spaces and owners earn from the ones they already have." />

        <div class="mt-14 grid gap-6 lg:grid-cols-2">
            <div class="card-transition rounded-3xl border border-indigo-100 bg-[#EEEFF7]/70 p-8">
                <div class="flex items-center gap-4">
                    <span class="flex size-12 items-center justify-center rounded-xl bg-indigo-900 text-lg text-white shadow-lg shadow-indigo-900/25" aria-hidden="true">
                        <i class="fa-solid fa-camera"></i>
                    </span>
                    <h3 class="text-xl font-semibold text-indigo-950">For customers</h3>
                </div>
                <ul class="mt-6 space-y-4">
                    <li class="flex gap-3">
                        <span class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs text-indigo-700" aria-hidden="true"><i class="fa-solid fa-check"></i></span>
                        <span class="text-sm text-gray-600 leading-relaxed">Every space is reviewed before it goes live, so what you see is what you get.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs text-indigo-700" aria-hidden="true"><i class="fa-solid fa-check"></i></span>
                        <span class="text-sm text-gray-600 leading-relaxed">Filter by city, category and budget — then compare real photos and prices side by side.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs text-indigo-700" aria-hidden="true"><i class="fa-solid fa-check"></i></span>
                        <span class="text-sm text-gray-600 leading-relaxed">Book in minutes and pay securely online, with instant owner confirmation.</span>
                    </li>
                </ul>
            </div>

            <div class="card-transition rounded-3xl border border-indigo-100 bg-[#EEEFF7]/70 p-8">
                <div class="flex items-center gap-4">
                    <span class="flex size-12 items-center justify-center rounded-xl bg-indigo-900 text-lg text-white shadow-lg shadow-indigo-900/25" aria-hidden="true">
                        <i class="fa-solid fa-store"></i>
                    </span>
                    <h3 class="text-xl font-semibold text-indigo-950">For location owners</h3>
                </div>
                <ul class="mt-6 space-y-4">
                    <li class="flex gap-3">
                        <span class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs text-indigo-700" aria-hidden="true"><i class="fa-solid fa-check"></i></span>
                        <span class="text-sm text-gray-600 leading-relaxed">Turn an idle space into monthly income — you set the hourly rate.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs text-indigo-700" aria-hidden="true"><i class="fa-solid fa-check"></i></span>
                        <span class="text-sm text-gray-600 leading-relaxed">You stay in control: approve or decline every booking request.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs text-indigo-700" aria-hidden="true"><i class="fa-solid fa-check"></i></span>
                        <span class="text-sm text-gray-600 leading-relaxed">Track bookings, reviews and earnings from a single owner dashboard.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- ======================= TESTIMONIALS ======================= --}}
<section class="relative overflow-hidden bg-indigo-950 py-16 sm:py-24 text-white" aria-labelledby="testimonials-heading">
    <div class="absolute -top-32 -right-32 size-96 rounded-full bg-indigo-600/20 blur-3xl" aria-hidden="true"></div>
    <div class="absolute -bottom-32 -left-32 size-96 rounded-full bg-fuchsia-500/10 blur-3xl" aria-hidden="true"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto text-center">
            <p class="text-xs sm:text-sm font-semibold uppercase tracking-widest text-indigo-300">Testimonials</p>
            <h2 id="testimonials-heading" class="mt-3 text-2xl sm:text-3xl lg:text-4xl font-semibold tracking-tight">Loved by customers and owners alike</h2>
        </div>

        <div x-data="{ active: 0 }"
             x-init="setInterval(() => active = (active + 1) % {{ count($testimonials) }}, 6000)"
             class="relative max-w-3xl mx-auto mt-14"
             aria-roledescription="carousel" aria-label="Customer testimonials">

            <div aria-live="polite">
                @foreach ($testimonials as $i => $testimonial)
                    <figure x-cloak
                            x-show="active === {{ $i }}"
                            x-transition.opacity.duration.500ms
                            :aria-hidden="active !== {{ $i }}"
                            class="rounded-3xl bg-white/5 ring-1 ring-white/10 backdrop-blur px-6 py-10 sm:px-12 text-center">
                        <div class="flex justify-center gap-1 text-sm text-amber-400" aria-label="Rated 5 out of 5">
                            <i class="fa-solid fa-star" aria-hidden="true"></i>
                            <i class="fa-solid fa-star" aria-hidden="true"></i>
                            <i class="fa-solid fa-star" aria-hidden="true"></i>
                            <i class="fa-solid fa-star" aria-hidden="true"></i>
                            <i class="fa-solid fa-star" aria-hidden="true"></i>
                        </div>
                        <blockquote class="mt-5 text-lg sm:text-xl leading-relaxed text-indigo-100">
                            “{{ $testimonial['quote'] }}”
                        </blockquote>
                        <figcaption class="mt-8 flex items-center justify-center gap-3">
                            <span class="flex size-11 items-center justify-center rounded-full bg-linear-to-br from-indigo-500 to-fuchsia-500 text-sm font-semibold" aria-hidden="true">
                                {{ $testimonial['initials'] }}
                            </span>
                            <span class="text-left">
                                <span class="block text-sm font-semibold">{{ $testimonial['name'] }}</span>
                                <span class="block text-xs text-indigo-300">{{ $testimonial['role'] }}</span>
                            </span>
                        </figcaption>
                    </figure>
                @endforeach
            </div>

            <div class="mt-8 flex items-center justify-center gap-4">
                <button type="button"
                        @click="active = (active - 1 + {{ count($testimonials) }}) % {{ count($testimonials) }}"
                        aria-label="Previous testimonial"
                        class="size-10 rounded-full bg-white/10 hover:bg-white/20 focus-visible:ring-2 focus-visible:ring-white transition-colors duration-200">
                    <i class="fa-solid fa-chevron-left text-sm" aria-hidden="true"></i>
                </button>

                <div class="flex items-center gap-2" role="group" aria-label="Choose a testimonial">
                    @foreach ($testimonials as $i => $testimonial)
                        <button type="button"
                                @click="active = {{ $i }}"
                                :aria-current="active === {{ $i }} ? 'true' : 'false'"
                                aria-label="Show testimonial {{ $i + 1 }} of {{ count($testimonials) }}"
                                class="size-2.5 rounded-full transition-colors duration-200"
                                :class="active === {{ $i }} ? 'bg-white' : 'bg-white/30 hover:bg-white/60'"></button>
                    @endforeach
                </div>

                <button type="button"
                        @click="active = (active + 1) % {{ count($testimonials) }}"
                        aria-label="Next testimonial"
                        class="size-10 rounded-full bg-white/10 hover:bg-white/20 focus-visible:ring-2 focus-visible:ring-white transition-colors duration-200">
                    <i class="fa-solid fa-chevron-right text-sm" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>
</section>

{{-- ========================= FINAL CTA ========================= --}}
<section class="bg-[#EEEFF7] py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-[2rem] bg-linear-to-r from-indigo-950 to-indigo-700 px-6 py-14 sm:px-14 sm:py-16 text-center text-white shadow-xl">
            <div class="absolute -top-24 -right-24 size-72 rounded-full bg-white/10 blur-3xl" aria-hidden="true"></div>
            <div class="absolute -bottom-28 -left-20 size-80 rounded-full bg-fuchsia-400/20 blur-3xl" aria-hidden="true"></div>

            <div class="relative max-w-2xl mx-auto">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-semibold tracking-tight">Ready to find your next location?</h2>
                <p class="mt-4 text-indigo-100 leading-relaxed">
                    @guest
                        Search our spaces for free — create an account to view full
                        details, prices and book your first shoot.
                    @else
                        Browse vetted spaces and book your next shoot in minutes.
                    @endguest
                </p>

                <div class="mt-9 flex flex-col sm:flex-row items-center justify-center gap-3">
                    @guest
                        <a href="{{ route('register') }}"
                           class="w-full sm:w-auto px-7 py-3 rounded-xl bg-white text-indigo-950 font-medium shadow-sm hover:bg-indigo-50 focus-visible:ring-2 focus-visible:ring-white transition-colors duration-200">
                            Create a free account
                        </a>
                    @else
                        <a href="{{ auth()->user()->role === \App\Enums\Role::Customer ? route('customer.listings') : route(auth()->user()->role->dashboardRoute()) }}"
                           class="w-full sm:w-auto px-7 py-3 rounded-xl bg-white text-indigo-950 font-medium shadow-sm hover:bg-indigo-50 focus-visible:ring-2 focus-visible:ring-white transition-colors duration-200">
                            Browse locations
                        </a>
                    @endguest

                    <a href="{{ route('about') }}"
                       class="w-full sm:w-auto px-7 py-3 rounded-xl border border-white/40 text-white font-medium hover:bg-white/10 focus-visible:ring-2 focus-visible:ring-white transition-colors duration-200">
                        Learn more about us
                    </a>
                </div>

                <p class="mt-6 text-xs text-indigo-300">Free to join · No listing fees to browse</p>
            </div>
        </div>
    </div>
</section>
</x-layout>
