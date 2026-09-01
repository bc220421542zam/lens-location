<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Seeds the marketing stat values shown on the home and about pages.
 * Naturally idempotent: updateOrCreate keeps each key's row in place.
 *
 *   php artisan db:seed --class=SettingsSeeder
 */
class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'home_locations'  => '500+',
            'home_shoots'     => '12,000+',
            'home_rating'     => '4.9★',
            'about_locations' => '500+',
            'about_roles'     => '3',
            'about_rating'    => '4.9★',
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->command?->info('Seeded '.count($defaults).' site settings.');
    }
}
