<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_api_root_returns_application_metadata(): void
    {
        $response = $this->getJson('/api');

        $response
            ->assertOk()
            ->assertJson([
                'name' => config('app.name'),
                'status' => 'ok',
            ]);
    }
}
