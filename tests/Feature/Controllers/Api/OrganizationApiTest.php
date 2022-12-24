<?php

namespace Tests\Feature\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class OrganizationApiTest extends TestCase
{
    /**
     * Test list Organization
     * vendor/bin/phpunit --filter testListOrganization tests/Feature/Controllers/Api/OrganizationApiTest.php
     * @return void
     */
    public function testListOrganization()
    {
        $user = User::where('email', 'admin@sample.com')->first();
        $this->actingAs($user, 'api');

        $response = $this->json('GET', 'api/v1/organizations?limit=10&page=1', ['Accept' => 'application/json']);

        $response->dump();
    }
}
