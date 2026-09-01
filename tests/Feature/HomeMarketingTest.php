<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeMarketingTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_renders_stat_defaults_without_settings(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('500+')
            ->assertSee('12,000+')
            ->assertSee('4.9★');
    }

    public function test_settings_override_home_stats(): void
    {
        Setting::updateOrCreate(['key' => 'home_locations'], ['value' => '900+']);
        Setting::updateOrCreate(['key' => 'home_shoots'], ['value' => '20,000+']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('900+')
            ->assertSee('20,000+')
            ->assertDontSee('12,000+');
    }

    public function test_about_renders_stat_defaults(): void
    {
        // Value and label are separate spans, so assert them in order.
        $this->get(route('about'))
            ->assertOk()
            ->assertSeeInOrder(['500+', 'vetted locations'])
            ->assertSee('roles, one marketplace')
            ->assertSeeInOrder(['4.9★', 'average rating']);
    }

    public function test_settings_override_about_stats(): void
    {
        Setting::updateOrCreate(['key' => 'about_locations'], ['value' => '750+']);
        Setting::updateOrCreate(['key' => 'about_roles'], ['value' => '5']);

        $this->get(route('about'))
            ->assertOk()
            ->assertSeeInOrder(['750+', 'vetted locations'])
            ->assertSee('roles, one marketplace')
            ->assertDontSee('500+');
    }
}
