<?php

namespace Tests\Feature\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class EventsApiTest extends TestCase
{
    /**
     * Test list Events
     * vendor/bin/phpunit --filter testListEvents tests/Feature/Controllers/Api/EventsApiTest.php
     * @return void
     */
    public function testListEvents()
    {
        $user = User::where('email', 'admin@sample.com')->first();
        $this->actingAs($user, 'api');

        $response = $this->json('GET', 'api/v1/events?limit=10&page=1', ['Accept' => 'application/json']);

        $response->dump();
    }
}
