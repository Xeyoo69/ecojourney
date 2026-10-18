<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Destination;
use App\Models\SustainabilityIndicator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportDestinationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_command_loads_the_national_dataset(): void
    {
        $this->artisan('destinations:import')
            ->expectsOutputToContain('Imported 107 destinations')
            ->assertExitCode(0);

        $this->assertDatabaseCount('destinations', 107);
        $this->assertGreaterThanOrEqual(4, Category::count());

        $waeRebo = Destination::where('slug', 'wae-rebo')->firstOrFail();
        $this->assertSame('Bali & Nusa Tenggara', $waeRebo->region);
        $this->assertSame(91, (int) $waeRebo->sustainability_score);
        $this->assertNotNull($waeRebo->category);

        $this->assertDatabaseHas('sustainability_indicators', [
            'destination_id' => $waeRebo->id,
        ]);
        $this->assertSame(2, Activity::where('destination_id', $waeRebo->id)->count());

        $regions = Destination::query()
            ->whereNotNull('region')
            ->distinct()
            ->pluck('region');

        $this->assertEqualsCanonicalizing(
            ['Sumatera', 'Jawa', 'Bali & Nusa Tenggara', 'Kalimantan', 'Sulawesi', 'Maluku', 'Papua'],
            $regions->all()
        );
    }

    public function test_import_command_skips_existing_slugs(): void
    {
        Category::create(['name' => 'Nature', 'slug' => 'nature']);

        $this->artisan('destinations:import')
            ->assertExitCode(0);

        $firstRunCount = Destination::count();
        $indicatorCount = SustainabilityIndicator::count();

        $this->artisan('destinations:import')
            ->expectsOutputToContain('Imported 0 destinations')
            ->assertExitCode(0);

        $this->assertSame($firstRunCount, Destination::count());
        $this->assertSame($indicatorCount, SustainabilityIndicator::count());
    }
}
