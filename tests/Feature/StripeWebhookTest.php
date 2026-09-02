<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\ListingStatus;
use App\Enums\PaymentStatus;
use App\Enums\PayoutStatus;
use App\Models\Booking;
use App\Models\Location;
use App\Models\StripeEvent;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\AdminDisputeNotification;
use App\Notifications\PaymentSuccessNotification;
use App\Support\BookingCompleter;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    private const SECRET = 'whsec_testsecret';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.stripe.webhook_secret' => self::SECRET]);
    }

    private function transaction(array $overrides = [], ?Carbon $bookingDate = null): Transaction
    {
        $owner = User::create([
            'role' => 'owner', 'first_name' => 'O', 'last_name' => 'Wner',
            'email' => 'owner'.uniqid().'@example.com', 'phone' => '03001234567',
            'password' => bcrypt('password'),
        ]);

        $customer = User::create([
            'role' => 'customer', 'first_name' => 'C', 'last_name' => 'Ust',
            'email' => 'cust'.uniqid().'@example.com', 'phone' => '03007654321',
            'password' => bcrypt('password'),
        ]);

        $location = Location::create([
            'user_id' => $owner->id, 'title' => 'Studio', 'description' => 'd',
            'address' => 'a', 'city' => 'Lahore', 'category' => 'Studio',
            'price_per_hour' => 2500, 'status' => ListingStatus::Approved,
        ]);

        $booking = Booking::create([
            'location_id' => $location->id, 'customer_id' => $customer->id,
            // Defaults to an already-finished shoot so the inline completion
            // path stays exercised.
            'booking_date' => $bookingDate ?? now()->subDay(), 'hours' => 4,
            'total_price' => 10000, 'status' => BookingStatus::Confirmed,
        ]);

        return Transaction::create(array_merge([
            'booking_id'                 => $booking->id,
            'customer_id'                => $customer->id,
            'owner_id'                   => $owner->id,
            'amount'                     => 10000,
            'platform_fee'               => 1000,
            'owner_earning'              => 9000,
            'currency'                   => 'PKR',
            'status'                     => PaymentStatus::Pending,
            'stripe_checkout_session_id' => 'cs_test_abc',
        ], $overrides));
    }

    /**
     * Build a payload signed the way Stripe signs it, so the controller's
     * verification runs for real rather than being stubbed out.
     */
    private function send(array $payload, ?string $secret = self::SECRET, ?int $timestamp = null)
    {
        $body      = json_encode($payload);
        $timestamp ??= time();
        $signature = hash_hmac('sha256', $timestamp.'.'.$body, $secret ?? '');

        return $this->call(
            'POST',
            '/stripe/webhook',
            [],
            [],
            [],
            ['HTTP_STRIPE_SIGNATURE' => "t={$timestamp},v1={$signature}", 'CONTENT_TYPE' => 'application/json'],
            $body,
        );
    }

    private function completedEvent(array $sessionOverrides = [], string $id = 'evt_1'): array
    {
        return [
            'id'      => $id,
            'object'  => 'event',
            'type'    => 'checkout.session.completed',
            'data'    => ['object' => array_merge([
                'id'             => 'cs_test_abc',
                'object'         => 'checkout.session',
                'payment_status' => 'paid',
                'payment_intent' => 'pi_test_abc',
                'amount_total'   => 1000000,
                'metadata'       => [],
            ], $sessionOverrides)],
        ];
    }

    // ------------------------------------------------------------- security

    public function test_missing_signature_is_rejected(): void
    {
        $transaction = $this->transaction();

        $this->postJson('/stripe/webhook', $this->completedEvent())
            ->assertStatus(400);

        $this->assertSame(PaymentStatus::Pending, $transaction->fresh()->status);
        $this->assertSame(0, StripeEvent::count());
    }

    public function test_forged_signature_is_rejected(): void
    {
        $transaction = $this->transaction();

        $this->send($this->completedEvent(), secret: 'whsec_wrongsecret')
            ->assertStatus(400);

        $this->assertSame(PaymentStatus::Pending, $transaction->fresh()->status);
        $this->assertSame(0, StripeEvent::count());
    }

    public function test_garbage_body_is_rejected(): void
    {
        $this->call(
            'POST', '/stripe/webhook', [], [], [],
            ['HTTP_STRIPE_SIGNATURE' => 't=1,v1=deadbeef', 'CONTENT_TYPE' => 'application/json'],
            'not json',
        )->assertStatus(400);
    }

    public function test_stale_timestamp_is_rejected(): void
    {
        $transaction = $this->transaction();

        // Stripe's default tolerance is 5 minutes.
        $this->send($this->completedEvent(), timestamp: time() - 3600)
            ->assertStatus(400);

        $this->assertSame(PaymentStatus::Pending, $transaction->fresh()->status);
    }

    // ---------------------------------------------------------- fulfilment

    public function test_valid_completed_session_marks_the_transaction_paid(): void
    {
        // Future shoot: the inline completion path stays out, so the payout
        // remains held until the booking actually completes.
        $transaction = $this->transaction(bookingDate: now()->addDay());

        $this->send($this->completedEvent())->assertOk();

        $transaction->refresh();

        $this->assertSame(PaymentStatus::Paid, $transaction->status);
        $this->assertSame('pi_test_abc', $transaction->stripe_payment_intent_id);
        $this->assertSame('pi_test_abc', $transaction->gateway_ref);
        $this->assertNotNull($transaction->paid_at);

        // Escrow: the money is held on the platform from the moment it lands.
        $this->assertSame(PayoutStatus::Held, $transaction->payout_status);
        $this->assertNotNull($transaction->held_since);
    }

    public function test_paid_session_notifies_the_customer(): void
    {
        $transaction = $this->transaction();

        $this->send($this->completedEvent())->assertOk();

        $notification = $transaction->customer->notifications()->sole();

        $this->assertSame('payment_success', $notification->data['type']);
        $this->assertSame('Payment Successful', $notification->data['title']);
        $this->assertStringContainsString('Studio', $notification->data['body']);
        $this->assertStringContainsString('10,000.00', $notification->data['body']);
    }

    public function test_payment_email_is_sent_when_email_notifications_are_enabled(): void
    {
        Notification::fake();

        $transaction = $this->transaction();

        $this->send($this->completedEvent())->assertOk();

        Notification::assertSentTo(
            $transaction->customer,
            PaymentSuccessNotification::class,
            fn ($notification, array $channels) => in_array('mail', $channels, true),
        );
    }

    public function test_payment_email_is_not_sent_when_email_notifications_are_disabled(): void
    {
        Notification::fake();

        $transaction = $this->transaction();
        $transaction->customer->update(['notif_email' => false]);

        $this->send($this->completedEvent())->assertOk();

        // The in-app notification still goes out - only the mail channel is
        // suppressed by the preference.
        Notification::assertSentTo(
            $transaction->customer,
            PaymentSuccessNotification::class,
            fn ($notification, array $channels) => ! in_array('mail', $channels, true),
        );
    }

    public function test_paid_session_completes_the_booking(): void
    {
        $transaction = $this->transaction();

        $this->send($this->completedEvent())->assertOk();

        $this->assertSame(BookingStatus::Completed, $transaction->booking->fresh()->status);

        // Completing the booking releases the escrow: the transaction becomes
        // eligible and the 90/10 split is recorded on the transaction.
        $transaction->refresh();

        $this->assertSame(PayoutStatus::Eligible, $transaction->payout_status);
        $this->assertSame('1000.00', $transaction->platform_commission);
        $this->assertSame('9000.00', $transaction->owner_payout_amount);
    }

    public function test_paid_session_does_not_complete_a_future_booking(): void
    {
        $transaction = $this->transaction(bookingDate: now()->addDay());

        $this->send($this->completedEvent())->assertOk();

        // Payment landed, but the shoot hasn't happened yet - BookingCompleter
        // promotes it on a later page load once the end time passes.
        $this->assertSame(PaymentStatus::Paid, $transaction->fresh()->status);
        $this->assertSame(BookingStatus::Confirmed, $transaction->booking->fresh()->status);
    }

    public function test_cancelled_booking_is_not_completed_by_payment(): void
    {
        $transaction = $this->transaction();
        $transaction->booking->update(['status' => BookingStatus::Cancelled]);

        $this->send($this->completedEvent())->assertOk();

        // The transaction still records the payment, but a booking cancelled
        // mid-checkout must not be revived.
        $this->assertSame(PaymentStatus::Paid, $transaction->fresh()->status);
        $this->assertSame(BookingStatus::Cancelled, $transaction->booking->fresh()->status);
    }

    public function test_amount_mismatch_does_not_complete_the_booking(): void
    {
        $transaction = $this->transaction();

        $this->send($this->completedEvent(['amount_total' => 100]))->assertOk();

        $this->assertSame(BookingStatus::Confirmed, $transaction->booking->fresh()->status);
    }

    public function test_replayed_event_is_a_noop(): void
    {
        $transaction = $this->transaction();

        $this->send($this->completedEvent())->assertOk();
        $paidAt = $transaction->fresh()->paid_at;

        // Same event id delivered again, as Stripe does on retry.
        $this->send($this->completedEvent())->assertOk();

        $this->assertSame(1, StripeEvent::count());
        $this->assertEquals($paidAt, $transaction->fresh()->paid_at);
    }

    public function test_amount_mismatch_does_not_mark_paid(): void
    {
        $transaction = $this->transaction();

        // Stripe reports 1.00 charged against a 10,000.00 booking.
        $this->send($this->completedEvent(['amount_total' => 100]))->assertOk();

        $this->assertSame(PaymentStatus::Pending, $transaction->fresh()->status);
        $this->assertNull($transaction->fresh()->paid_at);
    }

    public function test_unpaid_session_is_not_fulfilled(): void
    {
        $transaction = $this->transaction();

        // Delayed-notification methods complete the session while still unpaid.
        $this->send($this->completedEvent(['payment_status' => 'unpaid']))->assertOk();

        $this->assertSame(PaymentStatus::Pending, $transaction->fresh()->status);
    }

    public function test_async_payment_succeeded_also_fulfils(): void
    {
        $transaction = $this->transaction();

        $event         = $this->completedEvent();
        $event['type'] = 'checkout.session.async_payment_succeeded';

        $this->send($event)->assertOk();

        $this->assertSame(PaymentStatus::Paid, $transaction->fresh()->status);
    }

    public function test_expired_session_marks_the_transaction_failed(): void
    {
        $transaction = $this->transaction();

        $this->send([
            'id'   => 'evt_expired',
            'type' => 'checkout.session.expired',
            'data' => ['object' => ['id' => 'cs_test_abc', 'metadata' => []]],
        ])->assertOk();

        $this->assertSame(PaymentStatus::Failed, $transaction->fresh()->status);
    }

    public function test_charge_succeeded_does_not_touch_payout_status(): void
    {
        // Under escrow there is no destination transfer at charge time - the
        // payout stays held until the booking completes and the batch runs.
        $transaction = $this->transaction([
            'status'         => PaymentStatus::Paid,
            'payout_status'  => PayoutStatus::Held,
            'stripe_payment_intent_id' => 'pi_test_abc',
        ]);

        $this->send([
            'id'   => 'evt_charge',
            'type' => 'charge.succeeded',
            'data' => ['object' => [
                'id'             => 'ch_test_abc',
                'payment_intent' => 'pi_test_abc',
                'transfer'       => 'tr_test_abc',
            ]],
        ])->assertOk();

        $transaction->refresh();

        $this->assertNull($transaction->stripe_transfer_id);
        $this->assertSame(PayoutStatus::Held, $transaction->payout_status);
    }

    public function test_refund_cancels_the_booking(): void
    {
        $transaction = $this->transaction([
            'status'                   => PaymentStatus::Paid,
            'stripe_payment_intent_id' => 'pi_test_abc',
        ]);

        $this->send([
            'id'   => 'evt_refund',
            'type' => 'charge.refunded',
            'data' => ['object' => [
                'id'             => 'ch_test_abc',
                'payment_intent' => 'pi_test_abc',
            ]],
        ])->assertOk();

        $this->assertSame(PaymentStatus::Refunded, $transaction->fresh()->status);
        $this->assertSame(BookingStatus::Cancelled, $transaction->booking->fresh()->status);
    }

    public function test_dispute_flags_the_transaction_and_notifies_admins(): void
    {
        $admin = User::create([
            'role' => 'admin', 'first_name' => 'A', 'last_name' => 'Dmin',
            'email' => 'admin'.uniqid().'@example.com', 'phone' => '03009999999',
            'password' => bcrypt('password'),
        ]);

        $transaction = $this->transaction([
            'status'                   => PaymentStatus::Paid,
            'stripe_payment_intent_id' => 'pi_test_abc',
            'payout_status'            => PayoutStatus::Held,
        ]);

        $this->send([
            'id'   => 'evt_dispute',
            'type' => 'charge.dispute.created',
            'data' => ['object' => [
                'id'             => 'dp_test_abc',
                'payment_intent' => 'pi_test_abc',
            ]],
        ])->assertOk();

        $transaction->refresh();

        // Not a refund yet: the charge stays paid, but escrow freezes pending
        // admin review and the booking is not auto-completed.
        $this->assertNotNull($transaction->disputed_at);
        $this->assertSame(PaymentStatus::Paid, $transaction->status);

        $this->assertDatabaseHas('notifications', [
            'type'         => AdminDisputeNotification::class,
            'notifiable_id' => $admin->id,
        ]);
    }

    public function test_disputed_transaction_is_not_completed_by_later_settlement(): void
    {
        $transaction = $this->transaction([
            'status'                   => PaymentStatus::Paid,
            'stripe_payment_intent_id' => 'pi_test_abc',
            'payout_status'            => PayoutStatus::Held,
            'disputed_at'              => now(),
        ]);

        BookingCompleter::forAll();

        // The booking date has passed and payment was received, but the
        // dispute keeps it out of the auto-visit pass.
        $this->assertSame(BookingStatus::Confirmed, $transaction->booking->fresh()->status);
        $this->assertSame(PayoutStatus::Held, $transaction->fresh()->payout_status);
    }

    public function test_unhandled_event_type_is_acknowledged(): void
    {
        $this->send([
            'id'   => 'evt_other',
            'type' => 'customer.created',
            'data' => ['object' => ['id' => 'cus_123']],
        ])->assertOk();
    }
}
