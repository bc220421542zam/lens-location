<?php

namespace Tests\Unit;

use App\Support\StripeGateway;
use Stripe\StripeClient;
use Tests\TestCase;

class StripePayoutConversionTest extends TestCase
{
    private function gateway(): StripeGateway
    {
        // The client never calls the API from these paths - construction only.
        return new StripeGateway(new StripeClient('sk_test_unused'));
    }

    public function test_same_currency_transfer_keeps_amount_in_minor_units(): void
    {
        config([
            'services.stripe.currency'        => 'pkr',
            'services.stripe.payout_currency' => 'pkr',
        ]);

        $this->assertSame(468000, $this->gateway()->transferAmountMinor(4680.00));
    }

    public function test_pkr_to_usd_transfer_converts_at_the_configured_rate(): void
    {
        config([
            'services.stripe.currency'        => 'pkr',
            'services.stripe.payout_currency' => 'usd',
            'services.stripe.fx_pkr_to_usd'   => 0.0036,
        ]);

        $gateway = $this->gateway();

        $this->assertSame('usd', $gateway->payoutCurrency());

        // 45,000 PKR x 0.0036 = $162.00 -> 16200 cents
        $this->assertSame(16200, $gateway->transferAmountMinor(45000.00));

        // 1,620 PKR x 0.0036 = $5.832 -> rounds to 583 cents
        $this->assertSame(583, $gateway->transferAmountMinor(1620.00));

        // 4,680 PKR x 0.0036 = $16.848 -> rounds to 1685 cents
        $this->assertSame(1685, $gateway->transferAmountMinor(4680.00));
    }
}
