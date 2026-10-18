<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Itinerary;
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\Api\DestinationController as ApiDestinationController;
use App\Http\Controllers\Api\ExploreController as ApiExploreController;
use App\Http\Controllers\Api\FavoriteController as ApiFavoriteController;
use App\Http\Controllers\Api\HomeController as ApiHomeController;
use App\Http\Controllers\Api\PlannerController as ApiPlannerController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ApiControllersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Route::has('api.home')) {
            $this->registerApiRoutesForTests();
        }
    }

    private function registerApiRoutesForTests(): void
    {
        Route::middleware('web')->prefix('api')->name('api.')->group(function (): void {
            Route::get('/home', ApiHomeController::class)->name('home');
            Route::get('/destinations', [ApiExploreController::class, 'index'])->name('destinations.index');
            Route::get('/destinations/{slug}', [ApiDestinationController::class, 'show'])->name('destinations.show');
            Route::post('/auth/login', [ApiAuthController::class, 'login'])->middleware('guest')->name('auth.login');
            Route::post('/auth/register', [ApiAuthController::class, 'register'])->middleware('guest')->name('auth.register');
            Route::middleware('auth')->group(function (): void {
                Route::get('/auth/user', [ApiAuthController::class, 'user'])->name('auth.user');
                Route::post('/auth/logout', [ApiAuthController::class, 'logout'])->name('auth.logout');
                Route::post('/planner/generate', [ApiPlannerController::class, 'generate'])->name('planner.generate');
                Route::get('/planner/result/{id}', [ApiPlannerController::class, 'result'])->whereNumber('id')->name('planner.result');
                Route::get('/favorites', [ApiFavoriteController::class, 'index'])->name('favorites.index');
                Route::post('/favorites/{slug}', [ApiFavoriteController::class, 'store'])->name('favorites.store');
                Route::delete('/favorites/{slug}', [ApiFavoriteController::class, 'destroy'])->name('favorites.destroy');
                Route::delete('/favorites', [ApiFavoriteController::class, 'clear'])->name('favorites.clear');
            });
        });
    }

    public function test_public_endpoints_return_home_explore_and_destination_data(): void
    {
        $nature = Category::create([
            'name' => 'Nature',
            'slug' => 'nature',
        ]);
        $culture = Category::create([
            'name' => 'Culture',
            'slug' => 'culture',
        ]);
        $featured = Destination::create([
            'category_id' => $nature->id,
            'name' => 'Forest Retreat',
            'slug' => 'forest-retreat',
            'location' => 'North Region',
            'region' => 'North',
            'description' => 'Forest experience.',
            'sustainability_score' => 95,
        ]);
        $related = Destination::create([
            'category_id' => $culture->id,
            'name' => 'Village Walk',
            'slug' => 'village-walk',
            'location' => 'North Region',
            'region' => 'North',
            'description' => 'Village experience.',
            'sustainability_score' => 80,
        ]);
        Activity::create([
            'destination_id' => $featured->id,
            'name' => 'Guided hike',
        ]);

        $this->getJson('/api/home')
            ->assertOk()
            ->assertJsonPath('data.stats.destinations', 2)
            ->assertJsonPath('data.featured_destinations.0.slug', 'forest-retreat');

        $this->getJson('/api/destinations?category=nature&region=North&per_page=1')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'forest-retreat')
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('filters.categories.0.slug', 'culture')
            ->assertJsonPath('filters.regions.0', 'North');

        $this->getJson('/api/destinations/forest-retreat')
            ->assertOk()
            ->assertJsonPath('data.destination.slug', 'forest-retreat')
            ->assertJsonPath('data.destination.activities.0.name', 'Guided hike')
            ->assertJsonPath('data.related_destinations.0.slug', $related->slug);
    }

    public function test_session_auth_endpoints_create_and_end_session(): void
    {
        $this->postJson('/api/auth/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertCreated()
            ->assertJsonPath('data.email', 'jane@example.com')
            ->assertJsonPath('data.role', 'user');

        $this->getJson('/api/auth/user')
            ->assertOk()
            ->assertJsonPath('data.email', 'jane@example.com');

        $this->postJson('/api/auth/logout')->assertNoContent();

        // auth:sanctum calls shouldUse('sanctum') and caches guard instances;
        // in tests the container survives between requests, so reset both or
        // the next call would resolve a stale RequestGuard instead of the
        // fresh web session.
        $this->app['auth']->forgetGuards();
        $this->app['auth']->shouldUse('web');

        $this->getJson('/api/auth/user')->assertUnauthorized();

        User::factory()->create([
            'email' => 'login@example.com',
            'password' => 'password',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'login@example.com',
            'password' => 'password',
        ])->assertOk()
            ->assertJsonPath('data.email', 'login@example.com');
    }

    public function test_planner_only_returns_itineraries_owned_by_authenticated_user(): void
    {
        $category = Category::create([
            'name' => 'Nature',
            'slug' => 'nature',
        ]);
        $destination = Destination::create([
            'category_id' => $category->id,
            'name' => 'Forest Retreat',
            'slug' => 'forest-retreat',
            'location' => 'North Region',
            'region' => 'North',
            'description' => 'Forest experience.',
            'sustainability_score' => 95,
        ]);
        Activity::create([
            'destination_id' => $destination->id,
            'name' => 'Guided hike',
        ]);
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherItinerary = Itinerary::create([
            'user_id' => $otherUser->id,
            'name' => 'Other Journey',
            'duration' => 1,
            'preference' => 'nature',
        ]);

        $this->postJson('/api/planner/generate', [
            'duration' => 2,
            'preference' => 'nature',
        ])->assertUnauthorized();

        $response = $this->actingAs($user)->postJson('/api/planner/generate', [
            'duration' => 2,
            'preference' => 'nature',
        ]);

        $itineraryId = $response->assertCreated()
            ->assertJsonPath('data.duration', 2)
            ->assertJsonPath('data.items.0.destination.slug', 'forest-retreat')
            ->json('data.id');

        $this->assertDatabaseHas('itineraries', [
            'id' => $itineraryId,
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseCount('itinerary_items', 2);

        $this->actingAs($user)->getJson("/api/planner/result/{$itineraryId}")
            ->assertOk()
            ->assertJsonPath('data.id', $itineraryId);

        $this->actingAs($user)->getJson("/api/planner/result/{$otherItinerary->id}")
            ->assertNotFound();
    }

    public function test_favorites_require_authentication_and_are_scoped_per_user(): void
    {
        $category = Category::create([
            'name' => 'Nature',
            'slug' => 'nature',
        ]);
        $destination = Destination::create([
            'category_id' => $category->id,
            'name' => 'Forest Retreat',
            'slug' => 'forest-retreat',
            'location' => 'North Region',
            'region' => 'North',
            'description' => 'Forest experience.',
            'sustainability_score' => 95,
        ]);
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->getJson('/api/favorites')->assertUnauthorized();
        $this->postJson('/api/favorites/forest-retreat')->assertUnauthorized();
        $this->deleteJson('/api/favorites/forest-retreat')->assertUnauthorized();

        $this->actingAs($user)
            ->postJson('/api/favorites/forest-retreat')
            ->assertCreated()
            ->assertJsonPath('data.slug', 'forest-retreat');

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'destination_id' => $destination->id,
        ]);

        // Saving twice must stay idempotent.
        $this->actingAs($user)->postJson('/api/favorites/forest-retreat')->assertCreated();
        $this->assertDatabaseCount('favorites', 1);

        $this->actingAs($user)
            ->getJson('/api/favorites')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'forest-retreat')
            ->assertJsonPath('slugs.0', 'forest-retreat');

        $this->actingAs($otherUser)
            ->getJson('/api/favorites')
            ->assertOk()
            ->assertJsonCount(0, 'data')
            ->assertJsonCount(0, 'slugs');

        $this->actingAs($user)->deleteJson('/api/favorites/forest-retreat')->assertOk();
        $this->assertDatabaseCount('favorites', 0);

        $this->actingAs($user)->postJson('/api/favorites/forest-retreat')->assertCreated();
        $this->actingAs($user)->deleteJson('/api/favorites')->assertOk();
        $this->assertDatabaseCount('favorites', 0);

        $this->actingAs($user)->postJson('/api/favorites/unknown-place')->assertNotFound();
    }
}
