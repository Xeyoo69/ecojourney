<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiCacheHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_responses_may_not_be_stored_by_any_cache(): void
    {
        $response = $this->getJson('/api/categories');

        $response->assertSuccessful();
        $this->assertStringContainsString(
            'no-store',
            (string) $response->headers->get('Cache-Control')
        );
    }

    public function test_authenticated_auth_state_may_not_be_stored_by_any_cache(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->getJson('/api/auth/user');

        $response->assertOk();
        $this->assertStringContainsString(
            'no-store',
            (string) $response->headers->get('Cache-Control')
        );
    }
}
