<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.google.client_id', 'test-client-id');
        Config::set('services.google.client_secret', 'test-client-secret');
        Config::set('services.google.redirect', 'http://localhost/auth/google/callback');
    }

    private function mockGoogleUser(string $id, string $email, string $name = 'Test User', ?string $avatar = 'https://example.com/avatar.jpg', bool $verified = true): void
    {
        $abstractUser = (new SocialiteUser)
            ->setRaw(['email_verified' => $verified])
            ->map([
                'id' => $id,
                'email' => $email,
                'name' => $name,
                'avatar' => $avatar,
            ]);

        Socialite::shouldReceive('driver->user')->andReturn($abstractUser);
    }

    public function test_redirect_route_points_to_google(): void
    {
        Socialite::shouldReceive('driver->redirect')->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));

        $response = $this->get('/auth/google');

        $response->assertRedirect('https://accounts.google.com/o/oauth2/auth');
    }

    public function test_google_callback_creates_new_user_with_user_role(): void
    {
        $this->mockGoogleUser('g-123', 'john@example.com', 'John Doe');

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect('/');
        $this->assertAuthenticated();

        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('user', $user->role);
        $this->assertSame('g-123', $user->google_id);
        $this->assertSame('John Doe', $user->name);
        $this->assertNull($user->password);
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_google_callback_links_to_existing_user_account(): void
    {
        $existing = User::factory()->create([
            'email' => 'jane@example.com',
            'role' => 'user',
        ]);

        $this->mockGoogleUser('g-456', 'jane@example.com', 'Jane Doe');

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect('/');
        $this->assertAuthenticated();

        $existing->refresh();
        $this->assertSame('g-456', $existing->google_id);
        $this->assertSame('user', $existing->role);
    }

    public function test_google_callback_rejects_admin_accounts(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $this->mockGoogleUser('g-789', 'admin@example.com', 'Admin User');

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('login', ['oauth_error' => 'Admin accounts cannot sign in with Google. Please use your password instead.']));
        $this->assertGuest();

        $admin->refresh();
        $this->assertNull($admin->google_id);
    }

    public function test_google_callback_preserves_stored_redirect(): void
    {
        $this->mockGoogleUser('g-999', 'bob@example.com', 'Bob');

        $response = $this
            ->withSession(['google_oauth_redirect' => '/favorites'])
            ->get('/auth/google/callback');

        $response->assertRedirect('/favorites');
    }

    public function test_redirect_route_discards_protocol_relative_redirect(): void
    {
        Socialite::shouldReceive('driver->redirect')->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));

        $this->get('/auth/google?redirect=//evil.example.com');
        $this->mockGoogleUser('g-evil', 'victim@example.com', 'Victim');

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect('/');
    }

    public function test_google_callback_handles_oauth_error(): void
    {
        $response = $this->get('/auth/google/callback?error=access_denied');

        $response->assertRedirect(route('login', ['oauth_error' => 'Google sign-in was cancelled or failed.']));
        $this->assertGuest();
    }

    public function test_google_callback_rejects_unverified_email(): void
    {
        $this->mockGoogleUser('g-unverified', 'unverified@example.com', 'Unverified', null, false);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('login', ['oauth_error' => 'Your Google account email is not verified. Please verify it with Google and try again.']));
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'unverified@example.com']);
    }
}
