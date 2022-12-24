<?php

namespace Tests\Feature\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LogApiTest extends TestCase
{
    /**
     * A basic feature test example.
     * vendor/bin/phpunit --filter test_example tests/Feature/Controllers/Api/LogApiTest.php
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * Test list Log
     * vendor/bin/phpunit --filter testListLog tests/Feature/Controllers/Api/LogApiTest.php
     * @return void
     */
    public function testListLog()
    {
        $user = User::where('email', 'admin@sample.com')->first();
        $this->actingAs($user, 'api');

        $response = $this->json('GET', 'api/v1/logs?limit=10&page=1&sort=date', ['Accept' => 'application/json']);

        $response->dump();
    }

    /**
     * Test list Log
     * vendor/bin/phpunit --filter testSearchLog tests/Feature/Controllers/Api/LogApiTest.php
     * @return void
     */
    public function testSearchLog()
    {
        $user = User::where('email', 'admin@sample.com')->first();
        $this->actingAs($user, 'api');

        // $response = $this->json('GET', 'api/v1/logs?detail=ducimus&limit=10&page=1&sort=date', ['Accept' => 'application/json']);
        $response = $this->json('GET', 'api/v1/logs?type=2&limit=10&page=1&sort=date', ['Accept' => 'application/json']);
        // $response = $this->json('GET', 'api/v1/logs?detail=ducimus&type=2&limit=10&page=1&sort=date', ['Accept' => 'application/json']);

        $response->dump();
    }

    /**
     * Test Delete Log
     * vendor/bin/phpunit --filter testDeleteLog tests/Feature/Controllers/Api/LogApiTest.php
     * @return void
     */
    public function testDeleteLog()
    {
        $user = User::where('email', 'admin@sample.com')->first();
        $this->actingAs($user, 'api');

        $response = $this->json('DELETE', 'api/v1/logs/1ec69e87-4ad7-63ca-9908-5271b9d90d68', [], ['Accept' => 'application/json']);

        $response->dump();
    }


    /**
     * Test Delete Log
     * vendor/bin/phpunit --filter testDeleteLogUnauthorization tests/Feature/Controllers/Api/LogApiTest.php
     * @return void
     */
    public function testDeleteLogUnauthorization()
    {
        $user = User::where('email', 'chiyo03@example.com')->first();
        $this->actingAs($user, 'api');

        $response = $this->json('DELETE', 'api/v1/logs/1ec69e87-4ad7-63ca-9908-5271b9d90d68', [], ['Accept' => 'application/json']);

        // $response->dump();
        // $response->dumpHeaders();
        $response->assertStatus(403);
    }

    /**
     * Test Delete Log
     * vendor/bin/phpunit --filter testDeleteMultiLogs tests/Feature/Controllers/Api/LogApiTest.php
     * @return void
     */
    public function testDeleteMultiLogs()
    {
        $user = User::where('email', 'admin@sample.com')->first();
        $this->actingAs($user, 'api');

        $response = $this->json('DELETE', 'api/v1/logs/destroyMulti', 
            ['ids' => ['1ec68bdd-ea63-6400-b1ac-be7da517bb3b','1ec68bdd-ea77-6054-8041-be7da517bb3b']], 
            ['Accept' => 'application/json']);

        $response->dump();
    }
}
