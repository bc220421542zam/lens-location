<x-layout :title="'Privacy Policy'">

{{-- ========================= HERO ========================= --}}
<section class="relative overflow-hidden bg-linear-to-br from-indigo-950 via-indigo-900 to-indigo-800 text-white">
    <div class="absolute -top-24 -right-24 size-96 rounded-full bg-indigo-500/25 blur-3xl" aria-hidden="true"></div>
    <div class="absolute -bottom-40 -left-24 size-[28rem] rounded-full bg-fuchsia-500/20 blur-3xl" aria-hidden="true"></div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-20 sm:pt-24 sm:pb-28 text-center">
        <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-xs sm:text-sm font-medium text-indigo-100 ring-1 ring-white/20">
            <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
            Legal
        </p>
        <h1 class="mt-6 text-4xl sm:text-5xl font-semibold tracking-tight">Privacy Policy</h1>
        <p class="mt-4 text-sm text-indigo-200">Last updated: September 3, 2026</p>
    </div>
</section>

{{-- ========================= CONTENT ========================= --}}
<section class="bg-white py-14 sm:py-20">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12 text-gray-600 leading-relaxed">

        <div>
            <h2 class="text-xl font-semibold text-indigo-950">1. Who we are</h2>
            <p class="mt-3">
                LensLocation ("we", "us", "our") operates the marketplace at
                <span class="text-indigo-700">lenslocation.live</span>, where customers book photography
                locations and location owners rent their spaces out for shoots. This policy explains what
                personal information we collect, why we collect it, and the choices you have.
            </p>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-indigo-950">2. Information we collect</h2>
            <ul class="mt-3 space-y-3">
                <li class="flex gap-3">
                    <i class="fa-solid fa-user mt-1 text-indigo-600" aria-hidden="true"></i>
                    <span><strong class="text-indigo-950">Account information</strong> — your first and last name,
                    email address, phone number, role (customer, owner or admin) and password (stored
                    as a secure hash, never in plain text).</span>
                </li>
                <li class="flex gap-3">
                    <i class="fa-brands fa-google mt-1 text-indigo-600" aria-hidden="true"></i>
                    <span><strong class="text-indigo-950">Google / Facebook sign-in</strong> — when you use
                    "Continue with Google" or "Continue with Facebook", the provider shares your name and
                    email address with us, and we store the provider's identifier for your account.</span>
                </li>
                <li class="flex gap-3">
                    <i class="fa-solid fa-camera mt-1 text-indigo-600" aria-hidden="true"></i>
                    <span><strong class="text-indigo-950">Booking and payment information</strong> — bookings you
                    make or receive (dates, hours, shoot type), messages between customers and owners, and
                    reviews you leave. Card details never touch our servers: payments are processed by
                    Stripe, which receives only the transaction amount and purpose.</span>
                </li>
                <li class="flex gap-3">
                    <i class="fa-solid fa-gauge mt-1 text-indigo-600" aria-hidden="true"></i>
                    <span><strong class="text-indigo-950">Usage information</strong> — pages visited and
                    notification read-state, used to run the platform and improve it.</span>
                </li>
            </ul>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-indigo-950">3. How we use your information</h2>
            <ul class="mt-3 space-y-2">
                <li>To operate the marketplace: match bookings, notify owners of requests, and settle payments and payouts.</li>
                <li>To keep the marketplace safe: vet listings, moderate reviews, and prevent fraud or abuse.</li>
                <li>To communicate with you: booking status updates, payout confirmations, and important service notices.</li>
                <li>To meet our legal obligations, including financial record-keeping for payouts.</li>
            </ul>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-indigo-950">4. Who we share information with</h2>
            <ul class="mt-3 space-y-2">
                <li><strong class="text-indigo-950">Other users</strong> — the minimum needed for a booking:
                an owner sees the customer's name and booking details; a customer sees the location and owner name.</li>
                <li><strong class="text-indigo-950">Stripe</strong> — our payment processor, to charge customers
                and pay out owners. Stripe's own privacy policy applies to data it processes.</li>
                <li><strong class="text-indigo-950">Google and Facebook</strong> — only when you choose to sign
                in with them, to complete authentication.</li>
                <li><strong class="text-indigo-950">Authorities</strong> — where the law requires it.</li>
            </ul>
            <p class="mt-3">We never sell your personal information.</p>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-indigo-950">5. Cookies and local storage</h2>
            <p class="mt-3">
                We use a session cookie to keep you signed in and a CSRF cookie to protect forms. No
                advertising or tracking cookies are used.
            </p>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-indigo-950">6. How long we keep it</h2>
            <p class="mt-3">
                Account data is kept while your account is active and deleted when you ask us to
                (see our <a href="{{ route('data-deletion') }}" class="text-indigo-700 underline hover:text-indigo-900">Data Deletion</a> page).
                Financial records relating to bookings, payouts and fees are retained as long as tax and
                accounting rules require, even after account deletion.
            </p>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-indigo-950">7. Your rights</h2>
            <p class="mt-3">
                You may request a copy of the personal data we hold about you, ask us to correct it, or
                ask us to delete it. To exercise any of these rights, email
                <a href="mailto:lenzlocation@gmail.com" class="text-indigo-700 underline hover:text-indigo-900">lenzlocation@gmail.com</a>
                from the email address on your account.
            </p>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-indigo-950">8. Contact</h2>
            <p class="mt-3">
                Questions about this policy? Write to us at
                <a href="mailto:lenzlocation@gmail.com" class="text-indigo-700 underline hover:text-indigo-900">lenzlocation@gmail.com</a>.
            </p>
        </div>

        <div class="rounded-2xl bg-indigo-50/60 border border-indigo-100 p-5 text-sm">
            Also see:
            <a href="{{ route('terms-of-service') }}" class="text-indigo-700 underline hover:text-indigo-900 ml-1">Terms of Service</a>
            <span class="mx-1 text-indigo-300">·</span>
            <a href="{{ route('data-deletion') }}" class="text-indigo-700 underline hover:text-indigo-900">Data Deletion</a>
        </div>
    </div>
</section>
</x-layout>
