<x-layout :title="'Data Deletion'">

{{-- ========================= HERO ========================= --}}
<section class="relative overflow-hidden bg-linear-to-br from-indigo-950 via-indigo-900 to-indigo-800 text-white">
    <div class="absolute -top-24 -right-24 size-96 rounded-full bg-indigo-500/25 blur-3xl" aria-hidden="true"></div>
    <div class="absolute -bottom-40 -left-24 size-[28rem] rounded-full bg-fuchsia-500/20 blur-3xl" aria-hidden="true"></div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-20 sm:pt-24 sm:pb-28 text-center">
        <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-xs sm:text-sm font-medium text-indigo-100 ring-1 ring-white/20">
            <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
            Legal
        </p>
        <h1 class="mt-6 text-4xl sm:text-5xl font-semibold tracking-tight">Data Deletion</h1>
        <p class="mt-4 text-sm text-indigo-200">How to delete your account and personal data</p>
    </div>
</section>

{{-- ========================= CONTENT ========================= --}}
<section class="bg-white py-14 sm:py-20">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12 text-gray-600 leading-relaxed">

        <div>
            <h2 class="text-xl font-semibold text-indigo-950">1. How to request deletion</h2>
            <p class="mt-3">
                Send an email to
                <a href="mailto:lenzlocation@gmail.com" class="text-indigo-700 underline hover:text-indigo-900">lenzlocation@gmail.com</a>
                <strong class="text-indigo-950">from the email address registered on your account</strong>, with:
            </p>
            <ul class="mt-3 space-y-2 list-disc list-inside">
                <li>Subject line: <strong class="text-indigo-950">Data deletion request</strong></li>
                <li>The account email address</li>
                <li>(Optional) A note that you also want your Google / Facebook sign-in link removed</li>
            </ul>
            <p class="mt-3">
                We will confirm by reply and complete the deletion within 30 days.
            </p>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-indigo-950">2. What we delete</h2>
            <ul class="mt-3 space-y-2">
                <li>Your profile: name, email, phone, password, profile picture and preferences.</li>
                <li>Your Google / Facebook identifiers, unlinking your social sign-ins.</li>
                <li>Your bookings, messages and notifications.</li>
                <li>Your reviews — or, where a review must stay for the marketplace's integrity, it is anonymised so it no longer points to you.</li>
            </ul>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-indigo-950">3. What we must retain</h2>
            <p class="mt-3">
                Financial records — transaction amounts, fees, payout records and ledger entries — are kept
                for as long as tax and accounting law requires, in line with our
                <a href="{{ route('privacy-policy') }}" class="text-indigo-700 underline hover:text-indigo-900">Privacy Policy</a>.
                These records are not used for marketing and are only kept for legal compliance.
            </p>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-indigo-950">4. What you can do yourself right now</h2>
            <ul class="mt-3 space-y-3">
                <li class="flex gap-3">
                    <i class="fa-brands fa-google mt-1 text-indigo-600" aria-hidden="true"></i>
                    <span><strong class="text-indigo-950">Revoke Google access:</strong> Google Account →
                    Security → Your connections to third-party apps → LensLocation → Remove access.</span>
                </li>
                <li class="flex gap-3">
                    <i class="fa-brands fa-facebook mt-1 text-indigo-600" aria-hidden="true"></i>
                    <span><strong class="text-indigo-950">Revoke Facebook access:</strong> Facebook → Settings →
                    Apps and websites → LensLocation → Remove. This stops future data sharing with the app,
                    but to delete the data already stored with us, still email the request above.</span>
                </li>
                <li class="flex gap-3">
                    <i class="fa-solid fa-user-gear mt-1 text-indigo-600" aria-hidden="true"></i>
                    <span><strong class="text-indigo-950">Update your profile</strong> — you can edit your name,
                    phone and other profile fields any time from your account settings.</span>
                </li>
            </ul>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-indigo-950">5. Contact</h2>
            <p class="mt-3">
                Any questions about data deletion?
                <a href="mailto:lenzlocation@gmail.com" class="text-indigo-700 underline hover:text-indigo-900">lenzlocation@gmail.com</a>
            </p>
        </div>

        <div class="rounded-2xl bg-indigo-50/60 border border-indigo-100 p-5 text-sm">
            Also see:
            <a href="{{ route('privacy-policy') }}" class="text-indigo-700 underline hover:text-indigo-900 ml-1">Privacy Policy</a>
            <span class="mx-1 text-indigo-300">·</span>
            <a href="{{ route('terms-of-service') }}" class="text-indigo-700 underline hover:text-indigo-900">Terms of Service</a>
        </div>
    </div>
</section>
</x-layout>
