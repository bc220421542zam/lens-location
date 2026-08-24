<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // routes/web.php redirects '/' straight to the login screen, so 302 is
        // the correct response here - the stock 200 assertion never matched
        // this app.
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }
}
