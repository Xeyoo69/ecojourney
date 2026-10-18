<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\NationalController as ApiNationalController;
use App\Models\Category;
use App\Models\Destination;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class NationalEndpointTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Route::has('api.national')) {
            Route::middleware('web')->prefix('api')->name('api.')->group(function (): void {
                Route::get('/national', ApiNationalController::class)->name('national');
            });
        }
    }

    public function test_national_endpoint_returns_stats_regions_points_and_ranking(): void
    {
        $nature = Category::create([
            'name' => 'Nature',
            'slug' => 'nature',
        ]);
        $culture = Category::create([
            'name' => 'Culture',
            'slug' => 'culture',
        ]);

        Destination::create([
            'category_id' => $nature->id,
            'name' => 'Forest Peak',
            'slug' => 'forest-peak',
            'location' => 'North, Indonesia',
            'region' => 'North',
            'description' => 'Forest experience.',
            'latitude' => 2.5,
            'longitude' => 101.5,
            'sustainability_score' => 95,
        ]);
        Destination::create([
            'category_id' => $culture->id,
            'name' => 'River Village',
            'slug' => 'river-village',
            'location' => 'North, Indonesia',
            'region' => 'North',
            'description' => 'Village experience.',
            'latitude' => 3.5,
            'longitude' => 102.5,
            'sustainability_score' => 75,
        ]);
        Destination::create([
            'category_id' => $nature->id,
            'name' => 'Coral Bay',
            'slug' => 'coral-bay',
            'location' => 'South, Indonesia',
            'region' => 'South',
            'description' => 'Reef experience.',
            'latitude' => -5.5,
            'longitude' => 120.5,
            'sustainability_score' => 85,
        ]);
        Destination::create([
            'category_id' => $nature->id,
            'name' => 'Hidden Ridge',
            'slug' => 'hidden-ridge',
            'location' => 'Unlisted, Indonesia',
            'region' => null,
            'description' => 'Waiting for coordinates.',
            'sustainability_score' => 60,
        ]);

        $this->getJson('/api/national')
            ->assertOk()
            ->assertJsonPath('data.stats.destinations', 4)
            ->assertJsonPath('data.stats.regions', 2)
            ->assertJsonPath('data.stats.mapped', 3)
            ->assertJsonPath('data.stats.categories', 2)
            ->assertJsonPath('data.stats.avg_score', 79)
            ->assertJsonCount(3, 'data.points')
            ->assertJsonCount(2, 'data.regions')
            ->assertJsonPath('data.regions.0.region', 'North')
            ->assertJsonPath('data.regions.0.destinations', 2)
            ->assertJsonPath('data.regions.0.avg_score', 85)
            ->assertJsonPath('data.regions.0.best_destination.slug', 'forest-peak')
            ->assertJsonPath('data.regions.1.region', 'South')
            ->assertJsonPath('data.points.0.slug', 'forest-peak')
            ->assertJsonPath('data.points.2.slug', 'river-village')
            ->assertJsonPath('data.top_destinations.0.slug', 'forest-peak');

        $center = $this->getJson('/api/national')->json('data.regions.0.center');
        $this->assertEqualsWithDelta(3.0, $center['latitude'], 0.001);
        $this->assertEqualsWithDelta(102.0, $center['longitude'], 0.001);
    }

    public function test_national_spa_page_is_served(): void
    {
        $this->get('/national')
            ->assertOk()
            ->assertSee('id="app"', false);
    }

    public function test_national_payload_cache_invalidates_when_destinations_change(): void
    {
        $nature = Category::create([
            'name' => 'Nature',
            'slug' => 'nature',
        ]);

        Destination::create([
            'category_id' => $nature->id,
            'name' => 'First Peak',
            'slug' => 'first-peak',
            'location' => 'North, Indonesia',
            'region' => 'North',
            'description' => 'First experience.',
            'latitude' => 2.5,
            'longitude' => 101.5,
            'sustainability_score' => 90,
        ]);

        $this->getJson('/api/national')
            ->assertOk()
            ->assertJsonPath('data.stats.destinations', 1);

        Destination::create([
            'category_id' => $nature->id,
            'name' => 'Second Bay',
            'slug' => 'second-bay',
            'location' => 'South, Indonesia',
            'region' => 'South',
            'description' => 'Second experience.',
            'latitude' => -5.5,
            'longitude' => 120.5,
            'sustainability_score' => 80,
        ]);

        $this->getJson('/api/national')
            ->assertOk()
            ->assertJsonPath('data.stats.destinations', 2)
            ->assertJsonCount(2, 'data.points');
    }
}
