<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_attempts_are_rate_limited_per_ip(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson('/api/auth/login', [
                'email' => 'nobody@example.com',
                'password' => 'wrong-password',
            ])->assertStatus(422);
        }

        $this->postJson('/api/auth/login', [
            'email' => 'nobody@example.com',
            'password' => 'wrong-password',
        ])->assertStatus(429);
    }

    public function test_auth_endpoints_share_one_rate_limit_per_ip(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson('/api/auth/login', [
                'email' => 'nobody@example.com',
                'password' => 'wrong-password',
            ])->assertStatus(422);
        }

        $this->postJson('/api/auth/register', [
            'name' => 'User Overflow',
            'email' => 'overflow@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertStatus(429);
    }

    public function test_public_api_is_rate_limited_for_guests(): void
    {
        for ($request = 0; $request < 120; $request++) {
            $this->getJson('/api/home')->assertOk();
        }

        $this->getJson('/api/home')->assertStatus(429);
    }

    public function test_authenticated_users_get_a_higher_public_api_limit(): void
    {
        $user = User::factory()->create();

        for ($request = 0; $request < 125; $request++) {
            $this->actingAs($user)->getJson('/api/home')->assertOk();
        }
    }
}
