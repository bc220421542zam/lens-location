<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavbarAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_login_and_register_not_dashboard(): void
    {
        $response = $this->get(route('home'))->assertOk();

        $this->assertStringContainsString(
            'no-store',
            (string) $response->headers->get('Cache-Control'),
        );

        $html = $this->normalize($response->getContent());

        $this->assertStringContainsString('Login', $html);
        $this->assertStringContainsString('Register', $html);
        $this->assertStringNotContainsString('Browse Listings', $html);
        $this->assertStringNotContainsString('Dashboard', $html);
    }

    public function test_customer_sees_browse_listings_and_dashboard(): void
    {
        $html = $this->normalize($this->pageAs($this->makeUser('customer')));

        $this->assertStringContainsString('Browse Listings', $html);
        $this->assertStringContainsString('/customer/dashboard', $html);
        $this->assertStringContainsString('Dashboard', $html);
    }

    public function test_owner_and_admin_see_dashboard_but_not_browse_listings(): void
    {
        foreach (['owner' => '/owner/listings', 'admin' => '/admin/dashboard'] as $role => $dashboardPath) {
            $html = $this->normalize($this->pageAs($this->makeUser($role)));

            $this->assertStringContainsString($dashboardPath, $html);
            $this->assertStringContainsString('Dashboard', $html);
            $this->assertStringNotContainsString('Browse Listings', $html);
        }
    }

    private function pageAs(User $user): string
    {
        return $this->actingAs($user)->get(route('home'))->assertOk()->getContent();
    }

    private function normalize(string $html): string
    {
        return preg_replace('/\s+/', ' ', $html);
    }

    private function makeUser(string $role): User
    {
        return User::create([
            'role' => $role, 'first_name' => 'T', 'last_name' => 'U',
            'email' => $role.uniqid().'@example.com', 'phone' => '03001111111',
            'password' => bcrypt('password'),
        ]);
    }
}
