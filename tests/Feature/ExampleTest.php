<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // routes/web.php used to redirect '/' straight to the login screen;
        // the home page now serves openly, so 200 is the correct response.
        $response = $this->get('/');

        $response->assertOk();
    }
}
