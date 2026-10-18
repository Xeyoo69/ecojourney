<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\ReviewController as ApiReviewController;
use App\Models\Category;
use App\Models\Destination;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Route::has('api.reviews.index')) {
            $this->registerReviewRoutes();
        }
    }

    private function registerReviewRoutes(): void
    {
        Route::middleware('web')->prefix('api')->name('api.')->group(function (): void {
            Route::get('/destinations/{slug}/reviews', [ApiReviewController::class, 'index'])->name('reviews.index');
            Route::middleware('auth:sanctum')->group(function (): void {
                Route::post('/destinations/{slug}/reviews', [ApiReviewController::class, 'store'])
                    ->name('reviews.store');
                Route::delete('/destinations/{slug}/reviews/{id}', [ApiReviewController::class, 'destroy'])
                    ->name('reviews.destroy');
            });
        });
    }

    private function makeDestination(): Destination
    {
        $nature = Category::create(['name' => 'Nature', 'slug' => 'nature']);

        return Destination::create([
            'category_id' => $nature->id,
            'name' => 'Forest Retreat',
            'slug' => 'forest-retreat',
            'location' => 'North Region',
            'region' => 'North',
            'description' => 'Forest experience.',
            'sustainability_score' => 95,
        ]);
    }

    public function test_reviews_require_authentication_and_are_scoped_per_user(): void
    {
        $this->makeDestination();

        $this->postJson('/api/destinations/forest-retreat/reviews', [
            'rating' => 5,
            'comment' => 'Beautiful forest experience!',
        ])->assertUnauthorized();

        $this->deleteJson('/api/destinations/forest-retreat/reviews/1')->assertUnauthorized();

        $user = User::factory()->create();
        $this->actingAs($user)
            ->postJson('/api/destinations/forest-retreat/reviews', [
                'rating' => 5,
                'title' => 'Amazing!',
                'comment' => 'Beautiful forest experience with great guides.',
            ])
            ->assertCreated()
            ->assertJsonPath('data.rating', 5)
            ->assertJsonPath('data.title', 'Amazing!')
            ->assertJsonPath('data.user.name', $user->name);

        $this->assertDatabaseCount('reviews', 1);
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->getKey(),
            'destination_id' => 1,
            'rating' => 5,
        ]);
    }

    public function test_user_can_update_their_own_review_but_not_others(): void
    {
        $this->makeDestination();
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/destinations/forest-retreat/reviews', [
                'rating' => 4,
                'comment' => 'Good forest experience overall.',
            ])
            ->assertCreated();

        // Update own review.
        $this->actingAs($user)
            ->postJson('/api/destinations/forest-retreat/reviews', [
                'rating' => 5,
                'comment' => 'Updated: even better on second visit!',
            ])
            ->assertCreated()
            ->assertJsonPath('data.rating', 5);

        $this->assertDatabaseCount('reviews', 1);
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->getKey(),
            'rating' => 5,
        ]);

        // Other user cannot delete it.
        $reviewId = $user->reviews()->first()->getKey();
        $this->actingAs($otherUser)
            ->deleteJson("/api/destinations/forest-retreat/reviews/{$reviewId}")
            ->assertNotFound();

        $this->assertDatabaseCount('reviews', 1);
    }

    public function test_review_listing_and_destination_payload_include_average_rating(): void
    {
        $this->makeDestination();
        $userA = User::factory()->create(['name' => 'Alice']);
        $userB = User::factory()->create(['name' => 'Bob']);

        $this->actingAs($userA)->postJson('/api/destinations/forest-retreat/reviews', [
            'rating' => 5,
            'comment' => 'Absolutely stunning forest, highly recommend!',
        ])->assertCreated();

        $this->actingAs($userB)->postJson('/api/destinations/forest-retreat/reviews', [
            'rating' => 4,
            'comment' => 'Great place, just a bit crowded on weekends.',
        ])->assertCreated();

        $this->getJson('/api/destinations/forest-retreat/reviews')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.user.name', 'Bob')
            ->assertJsonPath('data.0.user.id', $userB->id)
            ->assertJsonPath('data.0.rating', 4)
            ->assertJsonPath('data.1.user.name', 'Alice')
            ->assertJsonPath('data.1.rating', 5);

        $this->getJson('/api/destinations/forest-retreat')
            ->assertOk()
            ->assertJsonPath('data.destination.rating.average', 4.5)
            ->assertJsonPath('data.destination.rating.count', 2);
    }

    public function test_review_validation_rules(): void
    {
        $this->makeDestination();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/destinations/forest-retreat/reviews', [
                'rating' => 6,
                'comment' => 'Too short',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['rating', 'comment']);

        $this->actingAs($user)
            ->postJson('/api/destinations/forest-retreat/reviews', [
                'rating' => 0,
                'comment' => 'Short comment.',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['rating']);
    }

    public function test_review_rate_limit_blocks_fourth_submission(): void
    {
        $this->makeDestination();
        $user = User::factory()->create();

        // Rate limit: 3 reviews / 5 minutes per user. Invalid submissions are rejected by
        // validation before the limiter, so only valid submissions consume the budget.
        $this->actingAs($user)->postJson('/api/destinations/forest-retreat/reviews', [
            'rating' => 5, 'comment' => 'First valid review with enough length.',
        ])->assertCreated();
        $this->actingAs($user)->postJson('/api/destinations/forest-retreat/reviews', [
            'rating' => 4, 'comment' => 'Second valid review with enough length.',
        ])->assertCreated();
        $this->actingAs($user)->postJson('/api/destinations/forest-retreat/reviews', [
            'rating' => 3, 'comment' => 'Third valid review with enough length.',
        ])->assertCreated();

        $this->actingAs($user)->postJson('/api/destinations/forest-retreat/reviews', [
            'rating' => 5, 'comment' => 'Fourth review should be blocked.',
        ])->assertStatus(429);
    }
}
