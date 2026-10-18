<?php

namespace App\Support;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Destination;
use App\Models\SustainabilityIndicator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

class DestinationImporter
{
    /**
     * Import destinations from a CSV file. Existing slugs are skipped.
     *
     * @return array{created: int, skipped: int, categories: int}
     */
    public function import(string $path): array
    {
        if (! File::exists($path)) {
            throw new RuntimeException("CSV file not found: {$path}");
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            throw new RuntimeException("Unable to open CSV file: {$path}");
        }

        try {
            $header = fgetcsv($handle);
            if ($header === false) {
                throw new RuntimeException('CSV file is empty.');
            }

            $header = array_map(fn (string $column): string => Str::slug($column, '_'), $header);
            $required = ['name', 'slug', 'location', 'region', 'category', 'description', 'latitude', 'longitude', 'sustainability_score'];

            foreach ($required as $column) {
                if (! in_array($column, $header, true)) {
                    throw new RuntimeException("CSV is missing the required column: {$column}");
                }
            }

            $categories = $this->categoryMap();
            $created = 0;
            $skipped = 0;

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) === 1 && trim((string) $row[0]) === '') {
                    continue;
                }

                $data = array_combine($header, array_pad($row, count($header), null));
                $slug = Str::slug((string) $data['slug']);

                if (Destination::where('slug', $slug)->exists()) {
                    $skipped++;

                    continue;
                }

                $categorySlug = Str::slug((string) $data['category']);
                if (! isset($categories[$categorySlug])) {
                    $categories[$categorySlug] = $this->createCategory((string) $data['category']);
                }

                $score = max(0, min(100, (int) $data['sustainability_score']));
                $destination = Destination::create([
                    'category_id' => $categories[$categorySlug]->id,
                    'name' => trim((string) $data['name']),
                    'slug' => $slug,
                    'location' => trim((string) $data['location']),
                    'region' => trim((string) $data['region']) ?: null,
                    'description' => trim((string) $data['description']),
                    'image' => trim((string) ($data['image'] ?? '')) ?: null,
                    'latitude' => (float) $data['latitude'],
                    'longitude' => (float) $data['longitude'],
                    'sustainability_score' => $score,
                ]);

                $this->createRelatedRecords($destination, $score);
                $created++;
            }

            return [
                'created' => $created,
                'skipped' => $skipped,
                'categories' => count($categories),
            ];
        } finally {
            fclose($handle);
        }
    }

    /**
     * @return array<string, Category>
     */
    private function categoryMap(): array
    {
        return Category::all()
            ->keyBy(fn (Category $category): string => Str::slug($category->slug))
            ->all();
    }

    private function createCategory(string $name): Category
    {
        $name = trim($name);

        return Category::create([
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => "Destinasi {$name}.",
        ]);
    }

    private function createRelatedRecords(Destination $destination, int $score): void
    {
        SustainabilityIndicator::create([
            'destination_id' => $destination->id,
            'eco_transport' => max(60, $score - 8),
            'waste_management' => max(60, $score - 6),
            'local_community' => min(98, $score + 2),
            'conservation' => min(98, $score + 4),
        ]);

        Activity::create([
            'destination_id' => $destination->id,
            'name' => 'Guided Exploration',
            'description' => 'Jelajahi ' . $destination->name . ' bersama pemandu lokal dengan prinsip wisata bertanggung jawab.',
            'duration' => '2–3 hours',
        ]);

        Activity::create([
            'destination_id' => $destination->id,
            'name' => 'Local Community Visit',
            'description' => 'Bertemu komunitas lokal dan dukung ekonomi setempat di ' . $destination->location . '.',
            'duration' => '2 hours',
        ]);
    }
}
