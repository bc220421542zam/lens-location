<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\ListingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Location;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\PaymentReceivedNotification;
use App\Notifications\PaymentSuccessNotification;
use App\Support\PaymentCompleter;
use App\Support\StripeGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * The checkout return page completes the payment synchronously, so a booking
 * no longer waits on the webhook to move confirmed -> completed.
 */
class PaymentReturnCompletionTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private User $owner;
    private Booking $booking;
    private Transaction $transaction;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::create([
            'role'       => 'owner',
            'first_name' => 'Test', 'last_name' => 'Owner',
            'email'      => 'owner'.uniqid().'@example.com',
            'phone'      => '03001234567',
            'password'   => bcrypt('password'),
        ]);

        $this->customer = User::create([
            'role'       => 'customer',
            'first_name' => 'Test', 'last_name' => 'Customer',
            'email'      => 'cust'.uniqid().'@example.com',
            'phone'      => '03007654321',
            'password'   => bcrypt('password'),
        ]);

        $location = Location::create([
            'user_id'        => $this->owner->id,
            'title'          => 'Rooftop Studio',
            'description'    => 'A studio.',
            'address'        => '1 Test Road',
            'city'           => 'Lahore',
            'category'       => 'Studio',
            'price_per_hour' => 2500,
            'status'         => ListingStatus::Approved,
        ]);

        $this->booking = Booking::create([
            'location_id'  => $location->id,
            'customer_id'  => $this->customer->id,
            'booking_date' => now()->addDay(),
            'hours'        => 4,
            'total_price'  => 10000,
            'status'       => BookingStatus::Confirmed,
        ]);

        $this->transaction = Transaction::create([
            'booking_id'                 => $this->booking->id,
            'customer_id'                => $this->customer->id,
            'owner_id'                   => $this->owner->id,
            'amount'                     => 10000,
            'platform_fee'               => 1000,
            'owner_earning'              => 9000,
            'currency'                   => 'pkr',
            'status'                     => PaymentStatus::Pending,
            'stripe_checkout_session_id' => 'cs_test_ret',
        ]);
    }

    /**
     * Swap the gateway for a stub whose retrieveSession returns a fixed
     * session, so the return page never makes a real API call.
     */
    private function fakeGateway(array $sessionOverrides = []): void
    {
        $stub = new class extends StripeGateway
        {
            public $session = null;

            public function __construct() {}

            public function retrieveSession(string $sessionId)
            {
                return $this->session;
            }
        };

        $stub->session = (object) array_merge([
            'id'             => 'cs_test_ret',
            'payment_status' => 'paid',
            'amount_total'   => 1000000,
            'payment_intent' => 'pi_test_ret',
            'metadata'       => ['transaction_id' => (string) $this->transaction->id],
        ], $sessionOverrides);

        $this->app->instance(StripeGateway::class, $stub);
    }

    private function paidSession(): object
    {
        return (object) [
            'id'             => 'cs_test_ret',
            'payment_status' => 'paid',
            'amount_total'   => 1000000,
            'payment_intent' => 'pi_test_ret',
            'metadata'       => ['transaction_id' => (string) $this->transaction->id],
        ];
    }

    public function test_return_page_marks_payment_paid_and_completes_booking(): void
    {
        Notification::fake();
        $this->fakeGateway();

        $this->actingAs($this->customer)
            ->get(route('customer.payments.success', ['session_id' => 'cs_test_ret']))
            ->assertRedirect(route('customer.bookings'))
            ->assertSessionHas('success', 'Payment successful. Your booking is confirmed.');

        $this->assertSame(PaymentStatus::Paid, $this->transaction->fresh()->status);
        $this->assertSame(BookingStatus::Completed, $this->booking->fresh()->status);

        Notification::assertSentTo($this->customer, PaymentSuccessNotification::class);
        Notification::assertSentTo($this->owner, PaymentReceivedNotification::class);
    }

    public function test_return_page_waits_when_session_still_unpaid(): void
    {
        $this->fakeGateway(['payment_status' => 'unpaid']);

        $this->actingAs($this->customer)
            ->get(route('customer.payments.success', ['session_id' => 'cs_test_ret']))
            ->assertRedirect(route('customer.bookings'))
            ->assertSessionHas('success', 'Payment received - we are confirming it with Stripe now.');

        $this->assertSame(PaymentStatus::Pending, $this->transaction->fresh()->status);
        $this->assertSame(BookingStatus::Confirmed, $this->booking->fresh()->status);
    }

    public function test_return_page_after_webhook_does_not_double_notify(): void
    {
        Notification::fake();

        // The webhook wins the race and completes the payment first.
        PaymentCompleter::forSession($this->paidSession());
        Notification::assertSentTo($this->customer, PaymentSuccessNotification::class, 1);

        $this->fakeGateway();

        $this->actingAs($this->customer)
            ->get(route('customer.payments.success', ['session_id' => 'cs_test_ret']))
            ->assertRedirect(route('customer.bookings'))
            ->assertSessionHas('success', 'Payment successful. Your booking is confirmed.');

        Notification::assertSentTo($this->customer, PaymentSuccessNotification::class, 1);
        Notification::assertSentTo($this->owner, PaymentReceivedNotification::class, 1);
    }

    public function test_return_page_does_not_complete_booking_for_other_customer(): void
    {
        $this->fakeGateway();

        $other = User::create([
            'role'       => 'customer',
            'first_name' => 'Other', 'last_name' => 'Customer',
            'email'      => 'other'.uniqid().'@example.com',
            'phone'      => '03009998888',
            'password'   => bcrypt('password'),
        ]);

        $this->actingAs($other)
            ->get(route('customer.payments.success', ['session_id' => 'cs_test_ret']))
            ->assertRedirect(route('customer.bookings'));

        $this->assertSame(PaymentStatus::Pending, $this->transaction->fresh()->status);
        $this->assertSame(BookingStatus::Confirmed, $this->booking->fresh()->status);
    }
}
