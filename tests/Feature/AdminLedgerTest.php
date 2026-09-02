<?php

namespace Tests\Feature;

use App\Enums\ListingStatus;
use App\Enums\PayoutBatchStatus;
use App\Enums\PaymentStatus;
use App\Enums\PayoutStatus;
use App\Models\Booking;
use App\Models\Location;
use App\Models\Payout;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLedgerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mid-month so the weekly buckets of August are deterministic.
        Carbon::setTestNow('2026-08-15 12:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    private User $admin;

    private User $owner;

    private User $customer;

    private Transaction $heldTxn;

    private Transaction $paidOutTxn;

    private function setUpFixture(): void
    {
        $this->admin = User::create([
            'role' => 'admin', 'first_name' => 'A', 'last_name' => 'Dmin',
            'email' => 'admin'.uniqid().'@example.com', 'phone' => '03001112222',
            'password' => bcrypt('password'),
        ]);

        $this->owner = User::create([
            'role' => 'owner', 'first_name' => 'O', 'last_name' => 'Wner',
            'email' => 'owner'.uniqid().'@example.com', 'phone' => '03001234567',
            'password' => bcrypt('password'),
            'stripe_account_id' => 'acct_test_1', 'stripe_transfers_status' => 'active',
        ]);

        $this->customer = User::create([
            'role' => 'customer', 'first_name' => 'Ahmed', 'last_name' => 'Raza',
            'email' => 'cust'.uniqid().'@example.com', 'phone' => '03007654321',
            'password' => bcrypt('password'),
        ]);

        $location = Location::create([
            'user_id' => $this->owner->id, 'title' => 'Studio', 'description' => 'd',
            'address' => 'a', 'city' => 'Lahore', 'category' => 'studio',
            'price_per_hour' => 6000, 'status' => ListingStatus::Approved,
        ]);

        $makeTxn = function (Carbon $paidAt, PayoutStatus $payoutStatus) use ($location) {
            $booking = Booking::create([
                'location_id' => $location->id,
                'customer_id' => $this->customer->id,
                'booking_date' => $paidAt->copy()->subDay(),
                'hours' => 4,
                'total_price' => 24000,
                'status' => $payoutStatus === PayoutStatus::Held ? 'confirmed' : 'completed',
            ]);

            return Transaction::create([
                'booking_id' => $booking->id,
                'customer_id' => $this->customer->id,
                'owner_id' => $this->owner->id,
                'amount' => 24000,
                'platform_fee' => 2400,
                'owner_earning' => 21600,
                'platform_commission' => 2400,
                'owner_payout_amount' => 21600,
                'currency' => 'PKR',
                'status' => PaymentStatus::Paid,
                'payout_status' => $payoutStatus,
                'paid_at' => $paidAt,
                'held_since' => $paidAt,
            ]);
        };

        // One held transaction this month (paid Aug 10).
        $this->heldTxn = $makeTxn(Carbon::create(2026, 8, 10, 14, 0, 0), PayoutStatus::Held);

        // One paid-out transaction this month with a processed batch.
        $this->paidOutTxn = $makeTxn(Carbon::create(2026, 8, 12, 10, 0, 0), PayoutStatus::PaidOut);

        $payout = Payout::create([
            'owner_id' => $this->owner->id,
            'total_amount' => 21600,
            'period_start' => Carbon::create(2026, 8, 10, 0, 0, 0),
            'period_end' => Carbon::create(2026, 8, 16, 23, 59, 59),
            'status' => PayoutBatchStatus::Processed,
            'processed_at' => Carbon::create(2026, 8, 13, 9, 0, 0),
        ]);

        $payout->transactions()->attach($this->paidOutTxn->id);
    }

    public function test_ledger_renders_summary_table_and_badges(): void
    {
        $this->setUpFixture();

        $this->actingAs($this->admin)
            ->get(route('admin.ledger'))
            ->assertOk()
            ->assertSee('Bookings &amp; payments', false)
            ->assertSee('Rs 48,000')       // received this month
            ->assertSee('Rs 4,800')        // commission
            ->assertSee('Rs 21,600')       // paid to owners
            ->assertSee('BK-'.str_pad((string) $this->heldTxn->booking_id, 5, '0', STR_PAD_LEFT))
            ->assertSee('Ahmed Raza')
            ->assertSee('Received')
            ->assertSee('On platform')
            ->assertSee('Transferred')
            ->assertSee('5 days')          // Aug 10 -> Aug 15
            ->assertSee('Aug 13, 2026');   // transferred on
    }

    public function test_ledger_weekly_and_monthly_toggles_switch_buckets(): void
    {
        $this->setUpFixture();

        $this->actingAs($this->admin)
            ->get(route('admin.ledger'))
            ->assertOk()
            ->assertSee('Week 1')
            ->assertSee('Week 2')
            ->assertSee('Weekly breakdown for August 2026');

        $this->actingAs($this->admin)
            ->get(route('admin.ledger', ['period' => 'monthly']))
            ->assertOk()
            ->assertSee('Jul')
            ->assertSee('Aug')
            ->assertDontSee('Week 1');
    }

    public function test_ledger_filters_narrow_the_table(): void
    {
        $this->setUpFixture();

        $this->actingAs($this->admin)
            ->get(route('admin.ledger', ['status' => 'paid', 'from' => '2026-08-11', 'to' => '2026-08-31']))
            ->assertOk()
            ->assertDontSee('BK-'.str_pad((string) $this->heldTxn->booking_id, 5, '0', STR_PAD_LEFT))
            ->assertSee('BK-'.str_pad((string) $this->paidOutTxn->booking_id, 5, '0', STR_PAD_LEFT));

        $this->actingAs($this->admin)
            ->get(route('admin.ledger', ['search' => 'Ahmed']))
            ->assertOk()
            ->assertSee('Ahmed Raza');
    }

    public function test_ledger_page_does_not_leak_alpine_component_source_as_text(): void
    {
        $this->setUpFixture();

        $html = $this->actingAs($this->admin)->get(route('admin.ledger'))->assertOk()->getContent();

        // The wrapper carries a short, single-quoted expression; the component
        // itself is registered in a script block via Alpine.data().
        $this->assertMatchesRegularExpression("/x-data='ledgerPage\(\{/", $html);
        $this->assertStringContainsString("Alpine.data('ledgerPage'", $html);

        // No part of the component source may render as visible page text
        // (regression: a broken quote used to close x-data early and dump the
        // raw JS into the page).
        $doc = new \DOMDocument();
        @$doc->loadHTML($html, LIBXML_NOWARNING | LIBXML_NOERROR);
        $xpath = new \DOMXPath($doc);
        $visibleText = '';
        foreach ($xpath->query('//*[not(self::script) and not(self::style)]/text()') as $node) {
            $visibleText .= ' '.$node->nodeValue;
        }

        foreach ([
            'Some filter values are invalid',
            'Could not refresh the ledger',
            'buildParams',
            'onPanelClick',
            'resetFilters',
            'this.filters.search',
        ] as $leak) {
            $this->assertStringNotContainsString($leak, $visibleText, "Alpine source leaked as visible text: {$leak}");
        }
    }

    public function test_ledger_exports_a_pdf(): void
    {
        $this->setUpFixture();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.ledger.export'));

        $response->assertOk();

        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
    }

    public function test_ledger_json_endpoint_returns_filtered_summary_for_date_window(): void
    {
        $this->setUpFixture();

        $this->actingAs($this->admin)
            ->getJson(route('admin.ledger', ['from' => '2026-08-11', 'to' => '2026-08-31']))
            ->assertOk()
            ->assertJsonPath('summary.bookings', 1)         // only the Aug 12 txn sits in the window
            ->assertJsonPath('summary.received', 24000.0)
            ->assertJsonPath('summary.commission', 2400.0)
            ->assertJsonPath('summary.paid_to_owners', 21600.0)
            ->assertJsonPath('summary.range_label', 'Aug 11, 2026 – Aug 31, 2026')
            ->assertJsonPath('count', 1);
    }

    public function test_ledger_json_endpoint_respects_search_and_status_filters(): void
    {
        $this->setUpFixture();

        // Both fixture rows are "paid", so a "pending" status zeroes the cards.
        $this->actingAs($this->admin)
            ->getJson(route('admin.ledger', ['status' => 'pending']))
            ->assertOk()
            ->assertJsonPath('summary.bookings', 0)
            ->assertJsonPath('summary.received', 0.0);

        // A search matching no customer narrows cards and table to zero.
        $this->actingAs($this->admin)
            ->getJson(route('admin.ledger', ['search' => 'Nobody']))
            ->assertOk()
            ->assertJsonPath('summary.bookings', 0)
            ->assertJsonPath('count', 0);
    }

    public function test_ledger_json_response_contains_rendered_partials(): void
    {
        $this->setUpFixture();

        $response = $this->actingAs($this->admin)->getJson(route('admin.ledger'));

        $response->assertOk()
            ->assertJsonPath('period', 'weekly')
            ->assertJsonStructure(['summary', 'trend', 'period', 'trend_subtitle', 'count', 'html', 'pagination', 'report_html', 'url']);

        $json = $response->json();

        $this->assertStringContainsString('Ahmed Raza', $json['html']);
        $this->assertStringContainsString('id="ledger-pagination"', $json['pagination']);
        $this->assertStringContainsString('Export PDF', $json['report_html']);
    }

    public function test_ledger_json_endpoint_rejects_invalid_date_range(): void
    {
        $this->setUpFixture();

        $this->actingAs($this->admin)
            ->getJson(route('admin.ledger', ['from' => '2026-08-15', 'to' => '2026-08-01']))
            ->assertStatus(422);
    }
}
