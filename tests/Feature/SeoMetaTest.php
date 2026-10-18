<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Destination;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoMetaTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_serves_default_seo_meta(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('<title>EcoJourney — Pariwisata Berkelanjutan Cerdas</title>', false)
            ->assertSee('property="og:title"', false)
            ->assertSee('name="twitter:card"', false)
            ->assertSee('rel="canonical"', false)
            ->assertSee('index, follow', false);
    }

    public function test_public_pages_have_unique_titles(): void
    {
        $this->get('/explore')
            ->assertOk()
            ->assertSee('Jelajahi Destinasi Eco di Seluruh Indonesia | EcoJourney', false);

        $this->get('/national')
            ->assertOk()
            ->assertSee('Peta Nasional Wisata Berkelanjutan | EcoJourney', false);

        $this->get('/about')
            ->assertOk()
            ->assertSee('Tentang EcoJourney', false);
    }

    public function test_destination_page_serves_rich_meta_and_json_ld(): void
    {
        $nature = Category::create(['name' => 'Nature', 'slug' => 'nature']);
        Destination::create([
            'category_id' => $nature->id,
            'name' => 'Coral Bay',
            'slug' => 'coral-bay',
            'location' => 'South, Indonesia',
            'region' => 'South',
            'description' => 'Reef experience with local guides.',
            'image' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1200&q=80',
            'latitude' => -5.5,
            'longitude' => 120.5,
            'sustainability_score' => 88,
        ]);

        $response = $this->get('/destinations/coral-bay');

        $response->assertOk()
            ->assertSee('Coral Bay — South, Indonesia | EcoJourney', false)
            ->assertSee('Reef experience with local guides.', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('TouristDestination', false)
            ->assertSee('rel="canonical"', false)
            ->assertSee('property="og:image"', false);

        $this->assertStringContainsString('"ratingValue":88', $response->getContent());
        $this->assertStringContainsString('"latitude":-5.5', $response->getContent());
    }

    public function test_unknown_destination_returns_soft_404_with_noindex(): void
    {
        $response = $this->get('/destinations/nowhere-place');

        $response->assertNotFound()
            ->assertSee('id="app"', false)
            ->assertSee('noindex', false)
            ->assertSee('Halaman tidak ditemukan | EcoJourney', false);
    }

    public function test_unknown_paths_return_404_and_private_pages_are_noindex(): void
    {
        $this->get('/this-route-does-not-exist')->assertNotFound();

        $this->get('/favorites')
            ->assertOk()
            ->assertSee('noindex, nofollow', false);

        $this->get('/login')
            ->assertOk()
            ->assertSee('noindex, nofollow', false);
    }

    public function test_sitemap_and_robots_are_served_with_destination_urls(): void
    {
        $nature = Category::create(['name' => 'Nature', 'slug' => 'nature']);
        Destination::create([
            'category_id' => $nature->id,
            'name' => 'Coral Bay',
            'slug' => 'coral-bay',
            'location' => 'South, Indonesia',
            'region' => 'South',
            'description' => 'Reef experience.',
            'latitude' => -5.5,
            'longitude' => 120.5,
            'sustainability_score' => 88,
        ]);

        $sitemap = $this->get('/sitemap.xml');

        $sitemap->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', false)
            ->assertSee(url('/'), false)
            ->assertSee(url('/destinations/coral-bay'), false);

        $robots = $this->get('/robots.txt');

        $robots->assertOk()
            ->assertSee('User-agent: *', false)
            ->assertSee('Sitemap: ' . url('/sitemap.xml'), false);
    }
}
