<?php

namespace Tests\Feature;

use App\Enums\ListingStatus;
use App\Enums\PaymentStatus;
use App\Enums\UserStatus;
use App\Models\Booking;
use App\Models\Location;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUsersTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'role' => 'admin', 'first_name' => 'A', 'last_name' => 'Dmin',
            'email' => 'admin'.uniqid().'@example.com', 'phone' => '03001112222',
            'password' => bcrypt('password'),
        ]);
    }

    private function makeUser(string $role = 'customer'): User
    {
        return User::create([
            'role' => $role, 'first_name' => 'U', 'last_name' => 'Ser',
            'email' => 'user'.uniqid().'@example.com', 'phone' => '03007654321',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_blocking_requires_a_reason(): void
    {
        $this->actingAs($this->admin());

        $user = $this->makeUser();

        $this->post(route('admin.users.toggle', $user))
            ->assertSessionHasErrors('reason');

        $this->assertSame(UserStatus::Active, $user->fresh()->status);
        $this->assertNull($user->fresh()->block_reason);
    }

    public function test_block_with_reason_blocks_the_user(): void
    {
        $this->actingAs($this->admin());

        $user = $this->makeUser();

        $this->post(route('admin.users.toggle', $user), ['reason' => 'Fraudulent booking'])
            ->assertRedirect();

        $user->refresh();

        $this->assertSame(UserStatus::Blocked, $user->status);
        $this->assertSame('Fraudulent booking', $user->block_reason);
        $this->assertNotNull($user->blocked_at);

        $this->assertStringContainsString('Fraudulent booking', $user->notifications()->first()->data['body']);
    }

    public function test_unblock_clears_the_reason(): void
    {
        $this->actingAs($this->admin());

        $user = $this->makeUser();

        $this->post(route('admin.users.toggle', $user), ['reason' => 'Spam listings']);
        $this->post(route('admin.users.toggle', $user));

        $user->refresh();

        $this->assertSame(UserStatus::Active, $user->status);
        $this->assertNull($user->block_reason);
        $this->assertNull($user->blocked_at);
    }

    public function test_blocked_users_list_shows_the_reason(): void
    {
        $this->actingAs($this->admin());

        $user = $this->makeUser();
        $user->update([
            'status'       => UserStatus::Blocked,
            'block_reason' => 'Repeated no-shows',
            'blocked_at'   => now(),
        ]);

        $this->get(route('admin.users'))
            ->assertOk()
            ->assertSee('Repeated no-shows');
    }

    public function test_user_detail_page_renders_for_any_role(): void
    {
        $this->actingAs($this->admin());

        $customer = $this->makeUser('customer');

        $this->get(route('admin.users.detail', $customer))
            ->assertOk()
            ->assertSee($customer->first_name)
            ->assertSee($customer->email);

        $owner = $this->makeUser('owner');

        $this->get(route('admin.users.detail', $owner))
            ->assertOk()
            ->assertSee($owner->email)
            ->assertSee('Stripe Transfers');
    }

    public function test_payments_page_links_to_user_detail(): void
    {
        $admin    = $this->admin();
        $owner    = $this->makeUser('owner');
        $customer = $this->makeUser('customer');

        $location = Location::create([
            'user_id' => $owner->id, 'title' => 'Studio', 'description' => 'd',
            'address' => 'a', 'city' => 'Lahore', 'category' => 'studio',
            'price_per_hour' => 1300, 'status' => ListingStatus::Approved,
        ]);

        $booking = Booking::create([
            'location_id' => $location->id,
            'customer_id' => $customer->id,
            'booking_date' => now()->subDay(),
            'hours' => 4,
            'total_price' => 5200,
            'status' => 'confirmed',
        ]);

        Transaction::create([
            'booking_id' => $booking->id,
            'customer_id' => $customer->id,
            'owner_id' => $owner->id,
            'amount' => 5200,
            'platform_fee' => 520,
            'owner_earning' => 4680,
            'status' => PaymentStatus::Paid,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.payments'))
            ->assertOk()
            ->assertSee(route('admin.users.detail', $owner->id), escape: false)
            ->assertSee(route('admin.users.detail', $customer->id), escape: false);
    }
}
