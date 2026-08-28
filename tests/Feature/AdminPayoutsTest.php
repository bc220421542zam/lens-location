<?php

namespace Tests\Feature;

use App\Enums\ListingStatus;
use App\Enums\PaymentStatus;
use App\Enums\PayoutStatus;
use App\Models\Booking;
use App\Models\Location;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPayoutsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $owner;

    private User $customer;

    private Location $location;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'role' => 'admin', 'first_name' => 'A', 'last_name' => 'Dmin',
            'email' => 'admin'.uniqid().'@example.com', 'phone' => '03001112222',
            'password' => bcrypt('password'),
        ]);

        $this->owner = User::create([
            'role' => 'owner', 'first_name' => 'O', 'last_name' => 'Wner',
            'email' => 'owner'.uniqid().'@example.com', 'phone' => '03001234567',
            'password' => bcrypt('password'),
        ]);

        $this->customer = User::create([
            'role' => 'customer', 'first_name' => 'C', 'last_name' => 'Ust',
            'email' => 'cust'.uniqid().'@example.com', 'phone' => '03007654321',
            'password' => bcrypt('password'),
        ]);

        $this->location = Location::create([
            'user_id' => $this->owner->id, 'title' => 'Studio', 'description' => 'd',
            'address' => 'a', 'city' => 'Lahore', 'category' => 'studio',
            'price_per_hour' => 1300, 'status' => ListingStatus::Approved,
        ]);
    }

    private function transaction(PaymentStatus $status, PayoutStatus $payoutStatus): Transaction
    {
        $booking = Booking::create([
            'location_id' => $this->location->id,
            'customer_id' => $this->customer->id,
            'booking_date' => now()->subDay(),
            'hours' => 4,
            'total_price' => 10000,
            'status' => 'confirmed',
        ]);

        return Transaction::create([
            'booking_id' => $booking->id,
            'customer_id' => $this->customer->id,
            'owner_id' => $this->owner->id,
            'amount' => 10000,
            'platform_fee' => 1000,
            'owner_earning' => 9000,
            'status' => $status,
            'payout_status' => $payoutStatus,
            'paid_at' => now(),
        ]);
    }

    public function test_ledger_shows_paid_transactions_with_payout_status(): void
    {
        $inTransit = $this->transaction(PaymentStatus::Paid, PayoutStatus::Unpaid);
        $transferred = $this->transaction(PaymentStatus::Paid, PayoutStatus::Paid);

        $this->actingAs($this->admin)
            ->get(route('admin.payouts'))
            ->assertOk()
            ->assertSee('#'.$inTransit->booking_id)
            ->assertSee('#'.$transferred->booking_id)
            ->assertSee('In transit')
            ->assertSee('Transferred')
            ->assertSee(number_format(9000, 2));
    }

    public function test_mark_sets_transferred_with_reference(): void
    {
        $transaction = $this->transaction(PaymentStatus::Paid, PayoutStatus::Unpaid);

        $this->actingAs($this->admin)
            ->post(route('admin.payouts.mark', $transaction), ['reference' => 'tr_test_123'])
            ->assertRedirect();

        $transaction->refresh();

        $this->assertSame(PayoutStatus::Paid, $transaction->payout_status);
        $this->assertSame('tr_test_123', $transaction->stripe_transfer_id);
        $this->assertNotNull($transaction->paid_at);
    }

    public function test_mark_defaults_reference_to_manual(): void
    {
        $transaction = $this->transaction(PaymentStatus::Paid, PayoutStatus::Unpaid);

        $this->actingAs($this->admin)
            ->post(route('admin.payouts.mark', $transaction))
            ->assertRedirect();

        $this->assertSame(PayoutStatus::Paid, $transaction->fresh()->payout_status);
        $this->assertSame('manual', $transaction->fresh()->stripe_transfer_id);
    }

    public function test_mark_refused_when_already_transferred(): void
    {
        $transaction = $this->transaction(PaymentStatus::Paid, PayoutStatus::Paid);
        $transaction->update(['stripe_transfer_id' => 'tr_existing']);

        $this->actingAs($this->admin)
            ->post(route('admin.payouts.mark', $transaction), ['reference' => 'tr_new'])
            ->assertStatus(422);

        $this->assertSame('tr_existing', $transaction->fresh()->stripe_transfer_id);
    }

    public function test_mark_refused_for_unpaid_status_transaction(): void
    {
        $transaction = $this->transaction(PaymentStatus::Pending, PayoutStatus::Unpaid);

        $this->actingAs($this->admin)
            ->post(route('admin.payouts.mark', $transaction))
            ->assertStatus(422);

        $this->assertSame(PayoutStatus::Unpaid, $transaction->fresh()->payout_status);
    }
}
