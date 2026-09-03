@php
    $owner   = auth()->user();
    $started = $owner->hasStartedStripeOnboarding();
    $active  = $owner->canReceivePayouts();
@endphp

<div class="card chart-transition shade bg-[#EEEFF7] p-4 md:p-5 rounded-2xl border-l-3 mb-4
            {{ $active ? 'border-green-600' : ($started ? 'border-yellow-600/60' : 'border-indigo-700') }}">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

        <div class="flex items-start gap-3 min-w-0">
            <div class="w-9 h-9 flex items-center justify-center rounded-full shrink-0
                        {{ $active ? 'bg-green-100 text-green-700' : ($started ? 'bg-yellow-50 text-yellow-600' : 'bg-indigo-100 text-indigo-700') }}">
                <i class="fa-solid {{ $active ? 'fa-circle-check' : ($started ? 'fa-hourglass-half' : 'fa-building-columns') }} text-sm"></i>
            </div>

            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <p class="font-semibold text-indigo-900">Stripe payouts</p>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                                 {{ $active ? 'bg-green-100 text-green-700' : ($started ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">
                        {{ $active ? 'Active' : ($started ? 'Verifying' : 'Not connected') }}
                    </span>
                </div>

                <p class="text-sm text-gray-500 mt-1">
                    @if ($active)
                        You're all set. Each booking payment is held in escrow and transferred to your Stripe
                        account once the booking date passes, less the {{ (int) round(config('services.stripe.commission_rate') * 100) }}% platform commission.
                    @elseif ($started)
                        Stripe is still reviewing your details. Customers can't pay for your listings until this clears.
                    @else
                        Connect a Stripe account to get paid. Customers can't pay for your listings until you do.
                    @endif
                </p>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 shrink-0">
            @if (! $started)
                <form method="POST" action="{{ route('owner.stripe.connect') }}">
                    @csrf
                    <button type="submit" class="btn btn-transition px-4 whitespace-nowrap">
                        Connect payouts
                    </button>
                </form>
            @else
                @unless ($active)
                    <form method="POST" action="{{ route('owner.stripe.connect') }}">
                        @csrf
                        <button type="submit" class="btn btn-transition px-4 whitespace-nowrap">
                            Continue setup
                        </button>
                    </form>

                    <form method="POST" action="{{ route('owner.stripe.refresh') }}">
                        @csrf
                        <button type="submit" class="action-btn shade grow-0 basis-auto px-4 whitespace-nowrap">
                            Check status
                        </button>
                    </form>
                @endunless

                <form method="POST" action="{{ route('owner.stripe.dashboard') }}">
                    @csrf
                    <button type="submit" class="action-btn shade grow-0 basis-auto px-4 whitespace-nowrap">
                        Open Stripe dashboard
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
