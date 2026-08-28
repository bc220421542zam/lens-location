<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\ListingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Location;
use App\Models\Transaction;
use App\Models\User;
use App\Support\StripeGateway;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StripeCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function owner(?string $transfersStatus = User::TRANSFERS_ACTIVE): User
    {
        return User::create([
            'role'                    => 'owner',
            'first_name'              => 'Test',
            'last_name'               => 'Owner',
            'email'                   => 'owner'.uniqid().'@example.com',
            'phone'                   => '03001234567',
            'password'                => bcrypt('password'),
            'stripe_account_id'       => $transfersStatus ? 'acct_test123' : null,
            'stripe_transfers_status' => $transfersStatus,
        ]);
    }

    private function customer(): User
    {
        return User::create([
            'role'       => 'customer',
            'first_name' => 'Test',
            'last_name'  => 'Customer',
            'email'      => 'customer'.uniqid().'@example.com',
            'phone'      => '03007654321',
            'password'   => bcrypt('password'),
        ]);
    }

    private function booking(User $customer, User $owner, BookingStatus $status, ?Carbon $bookingDate = null): Booking
    {
        $location = Location::create([
            'user_id'        => $owner->id,
            'title'          => 'Rooftop Studio',
            'description'    => 'A studio.',
            'address'        => '1 Test Road',
            'city'           => 'Lahore',
            'category'       => 'Studio',
            'price_per_hour' => 2500,
            'status'         => ListingStatus::Approved,
        ]);

        return Booking::create([
            'location_id'  => $location->id,
            'customer_id'  => $customer->id,
            'booking_date' => $bookingDate ?? now()->addDay(),
            'hours'        => 4,
            'total_price'  => 10000,
            'status'       => $status,
        ]);
    }

    /**
     * Swap the gateway for a stub so no HTTP call is made. Returns the stub so
     * tests can assert on what it was handed.
     */
    private function fakeGateway(): object
    {
        $stub = new class extends StripeGateway
        {
            public array $sessionArgs = [];

            public function __construct() {}

            public function currency(): string
            {
                return 'pkr';
            }

            public function commissionRate(): float
            {
                return 0.10;
            }

            public function checkoutSession($booking, $transaction, $destination, $successUrl, $cancelUrl)
            {
                $this->sessionArgs[] = compact('destination', 'successUrl', 'cancelUrl')
                    + ['transaction_id' => $transaction->id];

                return (object) [
                    'id'  => 'cs_test_'.count($this->sessionArgs),
                    'url' => 'https://checkout.stripe.test/session',
                ];
            }
        };

        $this->app->instance(StripeGateway::class, $stub);

        return $stub;
    }

    public function test_confirmed_booking_redirects_to_stripe_and_records_the_split(): void
    {
        $gateway  = $this->fakeGateway();
        $owner    = $this->owner();
        $customer = $this->customer();
        $booking  = $this->booking($customer, $owner, BookingStatus::Confirmed);

        $this->actingAs($customer)
            ->post(route('customer.bookings.pay', $booking))
            ->assertRedirect('https://checkout.stripe.test/session');

        $transaction = Transaction::where('booking_id', $booking->id)->sole();

        $this->assertSame('10000.00', $transaction->amount);
        $this->assertSame('1000.00', $transaction->platform_fee);
        $this->assertSame('9000.00', $transaction->owner_earning);
        $this->assertSame(PaymentStatus::Pending, $transaction->status);
        $this->assertSame('cs_test_1', $transaction->stripe_checkout_session_id);
        $this->assertSame('acct_test123', $gateway->sessionArgs[0]['destination']);

        // The three money columns must reconcile exactly.
        $this->assertSame(
            (float) $transaction->amount,
            (float) $transaction->platform_fee + (float) $transaction->owner_earning,
        );
    }

    public function test_paying_twice_reuses_the_same_pending_transaction(): void
    {
        $this->fakeGateway();
        $owner    = $this->owner();
        $customer = $this->customer();
        $booking  = $this->booking($customer, $owner, BookingStatus::Confirmed);

        $this->actingAs($customer)->post(route('customer.bookings.pay', $booking));
        $this->actingAs($customer)->post(route('customer.bookings.pay', $booking));

        $this->assertSame(1, Transaction::where('booking_id', $booking->id)->count());
    }

    public function test_another_customers_booking_is_forbidden(): void
    {
        $this->fakeGateway();
        $owner    = $this->owner();
        $booking  = $this->booking($this->customer(), $owner, BookingStatus::Confirmed);
        $intruder = $this->customer();

        $this->actingAs($intruder)
            ->post(route('customer.bookings.pay', $booking))
            ->assertForbidden();

        $this->assertSame(0, Transaction::count());
    }

    public function test_pending_booking_cannot_be_paid(): void
    {
        $this->fakeGateway();
        $owner    = $this->owner();
        $customer = $this->customer();
        $booking  = $this->booking($customer, $owner, BookingStatus::Pending);

        $this->actingAs($customer)
            ->from(route('customer.bookings'))
            ->post(route('customer.bookings.pay', $booking))
            ->assertRedirect(route('customer.bookings'))
            ->assertSessionHas('error');

        $this->assertSame(0, Transaction::count());
    }

    public function test_ended_booking_cannot_be_paid(): void
    {
        $this->fakeGateway();
        $owner    = $this->owner();
        $customer = $this->customer();
        $booking  = $this->booking($customer, $owner, BookingStatus::Confirmed, now()->subDay());

        $this->actingAs($customer)
            ->from(route('customer.bookings'))
            ->post(route('customer.bookings.pay', $booking))
            ->assertRedirect(route('customer.bookings'))
            ->assertSessionHas('error');

        $this->assertSame(0, Transaction::count());
    }

    public function test_booking_whose_owner_has_no_active_payouts_cannot_be_paid(): void
    {
        $this->fakeGateway();
        $owner    = $this->owner(null);
        $customer = $this->customer();
        $booking  = $this->booking($customer, $owner, BookingStatus::Confirmed);

        $this->actingAs($customer)
            ->from(route('customer.bookings'))
            ->post(route('customer.bookings.pay', $booking))
            ->assertRedirect(route('customer.bookings'))
            ->assertSessionHas('error');

        $this->assertSame(0, Transaction::count());
    }

    public function test_owner_still_verifying_cannot_be_paid(): void
    {
        $this->fakeGateway();
        $owner    = $this->owner('pending');
        $customer = $this->customer();
        $booking  = $this->booking($customer, $owner, BookingStatus::Confirmed);

        $this->actingAs($customer)
            ->from(route('customer.bookings'))
            ->post(route('customer.bookings.pay', $booking))
            ->assertSessionHas('error');

        $this->assertSame(0, Transaction::count());
    }

    public function test_already_paid_booking_cannot_be_paid_again(): void
    {
        $this->fakeGateway();
        $owner    = $this->owner();
        $customer = $this->customer();
        $booking  = $this->booking($customer, $owner, BookingStatus::Confirmed);

        Transaction::create([
            'booking_id'    => $booking->id,
            'customer_id'   => $customer->id,
            'owner_id'      => $owner->id,
            'amount'        => 10000,
            'platform_fee'  => 1000,
            'owner_earning' => 9000,
            'status'        => PaymentStatus::Paid,
        ]);

        $this->actingAs($customer)
            ->from(route('customer.bookings'))
            ->post(route('customer.bookings.pay', $booking))
            ->assertSessionHas('error');

        $this->assertSame(1, Transaction::where('booking_id', $booking->id)->count());
    }

    public function test_pay_route_rejects_get_so_it_cannot_be_prefetched(): void
    {
        $this->fakeGateway();
        $owner    = $this->owner();
        $customer = $this->customer();
        $booking  = $this->booking($customer, $owner, BookingStatus::Confirmed);

        $this->actingAs($customer)
            ->get('/customer/bookings/'.$booking->id.'/pay')
            ->assertStatus(405);
    }

    public function test_split_is_computed_in_integer_minor_units(): void
    {
        $gateway = new StripeGateway(
            $this->createMock(\Stripe\StripeClient::class),
        );

        config(['services.stripe.commission_rate' => 0.10]);

        // 1999.99 * 100 = 199999 minor; 10% = 20000; owner gets the remainder
        // exactly, with no floating-point drift.
        $split = $gateway->split(1999.99);

        $this->assertSame(199999, $split['amount_minor']);
        $this->assertSame(20000, $split['fee_minor']);
        $this->assertSame(179999, $split['owner_minor']);
        $this->assertSame(
            $split['amount_minor'],
            $split['fee_minor'] + $split['owner_minor'],
        );
    }
}
