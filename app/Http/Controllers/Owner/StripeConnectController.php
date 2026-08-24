<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Support\StripeGateway;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;

class StripeConnectController extends Controller
{
    public function __construct(private StripeGateway $stripe) {}

    /**
     * Create the connected account if needed, then hand the owner off to
     * Stripe-hosted onboarding.
     */
    public function onboard(): RedirectResponse
    {
        $owner = auth()->user();

        try {
            if (! $owner->stripe_account_id) {
                $accountId = $this->stripe->createConnectedAccount($owner);
                $owner->forceFill(['stripe_account_id' => $accountId])->save();
            }

            $url = $this->stripe->onboardingUrl(
                $owner->stripe_account_id,
                route('owner.stripe.connect'),   // refresh: mint a fresh link
                route('owner.stripe.return'),
            );
        } catch (ApiErrorException $e) {
            Log::error('Stripe Connect onboarding failed', [
                'user_id' => $owner->id,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'We could not reach Stripe. Please try again in a moment.');
        }

        return redirect()->away($url);
    }

    /**
     * Where Stripe sends the owner back. Onboarding can be abandoned midway, so
     * re-read the capability status rather than assuming success.
     */
    public function callbackReturn(): RedirectResponse
    {
        $owner = auth()->user();

        try {
            $this->stripe->syncAccountStatus($owner);
        } catch (ApiErrorException $e) {
            Log::error('Stripe account sync failed', [
                'user_id' => $owner->id,
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('owner.earnings')
                ->with('error', 'We could not confirm your Stripe status. Please refresh in a moment.');
        }

        return $owner->fresh()->canReceivePayouts()
            ? redirect()->route('owner.earnings')
                ->with('success', 'Payouts are active. Customers can now pay for your listings.')
            : redirect()->route('owner.earnings')
                ->with('error', 'Stripe still needs more information before you can receive payouts.');
    }

    /**
     * Refresh the capability status on demand, for owners waiting on
     * verification who don't want to redo onboarding.
     */
    public function refresh(): RedirectResponse
    {
        $owner = auth()->user();

        if (! $owner->stripe_account_id) {
            return back()->with('error', 'Connect your Stripe account first.');
        }

        try {
            $this->stripe->syncAccountStatus($owner);
        } catch (ApiErrorException $e) {
            Log::error('Stripe account sync failed', [
                'user_id' => $owner->id,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'We could not reach Stripe. Please try again in a moment.');
        }

        return back()->with('success', $owner->fresh()->canReceivePayouts()
            ? 'Payouts are active.'
            : 'Stripe is still reviewing your details.');
    }

    /**
     * Express dashboard login link so owners can see balances and payouts.
     */
    public function dashboard(): RedirectResponse
    {
        $owner = auth()->user();

        if (! $owner->stripe_account_id) {
            return back()->with('error', 'Connect your Stripe account first.');
        }

        try {
            $url = $this->stripe->dashboardUrl($owner->stripe_account_id);
        } catch (ApiErrorException $e) {
            Log::error('Stripe login link failed', [
                'user_id' => $owner->id,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Stripe could not open your dashboard. Complete onboarding first.');
        }

        return redirect()->away($url);
    }
}
