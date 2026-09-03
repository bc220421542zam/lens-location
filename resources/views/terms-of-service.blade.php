<x-layout :title="'Terms of Service'">

{{-- ========================= HERO ========================= --}}
<section class="relative overflow-hidden bg-linear-to-br from-indigo-950 via-indigo-900 to-indigo-800 text-white">
    <div class="absolute -top-24 -right-24 size-96 rounded-full bg-indigo-500/25 blur-3xl" aria-hidden="true"></div>
    <div class="absolute -bottom-40 -left-24 size-[28rem] rounded-full bg-fuchsia-500/20 blur-3xl" aria-hidden="true"></div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-20 sm:pt-24 sm:pb-28 text-center">
        <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-xs sm:text-sm font-medium text-indigo-100 ring-1 ring-white/20">
            <i class="fa-solid fa-scale-balanced" aria-hidden="true"></i>
            Legal
        </p>
        <h1 class="mt-6 text-4xl sm:text-5xl font-semibold tracking-tight">Terms of Service</h1>
        <p class="mt-4 text-sm text-indigo-200">Last updated: September 3, 2026</p>
    </div>
</section>

{{-- ========================= CONTENT ========================= --}}
<section class="bg-white py-14 sm:py-20">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12 text-gray-600 leading-relaxed">

        <div>
            <h2 class="text-xl font-semibold text-indigo-950">1. Agreement</h2>
            <p class="mt-3">
                These Terms of Service govern your use of LensLocation ("the platform") at
                <span class="text-indigo-700">lenslocation.live</span>. By creating an account or using the
                platform, you agree to these terms. If you do not agree, please do not use the platform.
            </p>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-indigo-950">2. Accounts and roles</h2>
            <ul class="mt-3 space-y-3">
                <li class="flex gap-3">
                    <i class="fa-solid fa-camera mt-1 text-indigo-600" aria-hidden="true"></i>
                    <span><strong class="text-indigo-950">Customers</strong> browse vetted locations, send
                    booking requests, and pay for confirmed bookings.</span>
                </li>
                <li class="flex gap-3">
                    <i class="fa-solid fa-store mt-1 text-indigo-600" aria-hidden="true"></i>
                    <span><strong class="text-indigo-950">Location owners</strong> list spaces, approve or
                    decline booking requests, and receive payouts through Stripe.</span>
                </li>
                <li class="flex gap-3">
                    <i class="fa-solid fa-user-tie mt-1 text-indigo-600" aria-hidden="true"></i>
                    <span><strong class="text-indigo-950">Admins</strong> vet listings, moderate reviews and
                    accounts, and oversee payouts.</span>
                </li>
            </ul>
            <p class="mt-3">
                You are responsible for keeping your login credentials safe and for everything that happens
                under your account.
            </p>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-indigo-950">3. Listings</h2>
            <p class="mt-3">
                Owners must have the right to rent out every space they list. Listings must describe the
                space accurately; every listing is reviewed by an admin before it goes live. LensLocation
                may remove any listing that is misleading, unsafe, or violates these terms.
            </p>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-indigo-950">4. Bookings and payments</h2>
            <ul class="mt-3 space-y-2">
                <li>A customer sends a booking request for a date, time and number of hours.</li>
                <li>The owner accepts or declines. A booking is only confirmed once the owner accepts.</li>
                <li>Payment is collected securely through Stripe after confirmation, at the owner's listed hourly rate.</li>
                <li>A 10% platform commission is deducted from each completed payment; owners receive the remaining 90%.</li>
                <li>Payouts to owners are sent to their connected Stripe account once the booking date has passed.</li>
            </ul>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-indigo-950">5. Cancellations, refunds and disputes</h2>
            <ul class="mt-3 space-y-2">
                <li>A customer may cancel a booking request while it is still pending.</li>
                <li>Refunds for paid bookings follow the outcome of the dispute process below.</li>
                <li>If something goes wrong, contact us or the other party; admins can mediate disputes and
                Stripe dispute tools remain available to both sides.</li>
                <li>Unpaid bookings whose date has passed expire automatically.</li>
            </ul>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-indigo-950">6. Reviews and conduct</h2>
            <p class="mt-3">
                Reviews must reflect genuine bookings. Reviews may be hidden or removed by admins if they are
                fake, abusive, or off-topic. You agree not to misuse the platform — including harassment,
                fraud, payment abuse, or attempts to book outside the platform to avoid fees.
            </p>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-indigo-950">7. Liability</h2>
            <p class="mt-3">
                LensLocation is a marketplace: it connects customers with owners but is not a party to the
                underlying rental. To the maximum extent permitted by law, the platform is provided "as is"
                and LensLocation is not liable for disputes between users, condition of premises, or
                indirect or consequential losses. Owners are responsible for the safety and legality of
                their spaces; customers shoot at their own risk.
            </p>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-indigo-950">8. Account suspension</h2>
            <p class="mt-3">
                We may suspend or block accounts that violate these terms, abuse the platform, or endanger
                other users, without prior notice where appropriate.
            </p>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-indigo-950">9. Changes to these terms</h2>
            <p class="mt-3">
                We may update these terms from time to time. The "Last updated" date above shows when the
                current version took effect; continued use of the platform after a change means you accept
                the new terms.
            </p>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-indigo-950">10. Contact</h2>
            <p class="mt-3">
                Questions about these terms? Email
                <a href="mailto:lenzlocation@gmail.com" class="text-indigo-700 underline hover:text-indigo-900">lenzlocation@gmail.com</a>.
            </p>
        </div>

        <div class="rounded-2xl bg-indigo-50/60 border border-indigo-100 p-5 text-sm">
            Also see:
            <a href="{{ route('privacy-policy') }}" class="text-indigo-700 underline hover:text-indigo-900 ml-1">Privacy Policy</a>
            <span class="mx-1 text-indigo-300">·</span>
            <a href="{{ route('data-deletion') }}" class="text-indigo-700 underline hover:text-indigo-900">Data Deletion</a>
        </div>
    </div>
</section>
</x-layout>
