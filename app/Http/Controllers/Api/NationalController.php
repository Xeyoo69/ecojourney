<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DestinationResource;
use App\Models\Activity;
use App\Models\Category;
use App\Models\Destination;
use App\Support\CacheKeys;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class NationalController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $payload = Cache::remember(
            CacheKeys::NATIONAL_PAYLOAD,
            now()->addMinutes(15),
            fn (): array => $this->buildPayload()
        );

        return response()->json(['data' => $payload]);
    }

    private function buildPayload(): array
    {
        $ranked = Destination::query()
            ->with('category')
            ->select([
                'id', 'name', 'slug', 'location', 'region', 'description', 'image',
                'latitude', 'longitude', 'sustainability_score', 'category_id', 'created_at', 'updated_at',
            ])
            ->orderByDesc('sustainability_score')
            ->orderBy('name')
            ->get();

        $mapped = $ranked->filter(
            fn (Destination $destination): bool => $destination->latitude !== null && $destination->longitude !== null
        );

        $aggregates = Destination::query()
            ->selectRaw('count(*) as total')
            ->selectRaw('count(distinct region) as regions')
            ->selectRaw('avg(sustainability_score) as avg_score')
            ->selectRaw('max(sustainability_score) as highest_score')
            ->first();

        $stats = [
            'destinations' => (int) $aggregates->total,
            'regions' => (int) $aggregates->regions,
            'categories' => Category::count(),
            'activities' => Activity::count(),
            'avg_score' => (int) round($aggregates->avg_score ?: 0),
            'highest_score' => (int) ($aggregates->highest_score ?? 0),
            'mapped' => $mapped->count(),
        ];

        return [
            'stats' => $stats,
            'regions' => $this->regionSummaries($ranked),
            'points' => $this->mapPoints($mapped),
            'top_destinations' => DestinationResource::collection($ranked->take(6)->values())->resolve(),
        ];
    }

    /**
     * @param  Collection<int, Destination>  $destinations
     * @return array<int, array<string, mixed>>
     */
    private function regionSummaries(Collection $destinations): array
    {
        return $destinations
            ->filter(fn (Destination $destination): bool => filled($destination->region))
            ->groupBy('region')
            ->map(function (Collection $group, string $region): array {
                $withCoordinates = $group->filter(
                    fn (Destination $destination): bool => $destination->latitude !== null && $destination->longitude !== null
                );
                $best = $group->sortByDesc('sustainability_score')->first();

                return [
                    'region' => $region,
                    'destinations' => $group->count(),
                    'avg_score' => (int) round($group->avg('sustainability_score')),
                    'highest_score' => (int) $group->max('sustainability_score'),
                    'center' => $withCoordinates->isNotEmpty() ? [
                        'latitude' => round((float) $withCoordinates->avg('latitude'), 4),
                        'longitude' => round((float) $withCoordinates->avg('longitude'), 4),
                    ] : null,
                    'best_destination' => [
                        'name' => $best->name,
                        'slug' => $best->slug,
                        'sustainability_score' => (int) $best->sustainability_score,
                    ],
                ];
            })
            ->sortBy([['destinations', 'desc'], ['region', 'asc']])
            ->values()
            ->all();
    }

    /**
     * @param  Collection<int, Destination>  $destinations
     * @return array<int, array<string, mixed>>
     */
    private function mapPoints(Collection $destinations): array
    {
        return $destinations
            ->map(fn (Destination $destination): array => [
                'id' => $destination->id,
                'name' => $destination->name,
                'slug' => $destination->slug,
                'location' => $destination->location,
                'region' => $destination->region,
                'latitude' => (float) $destination->latitude,
                'longitude' => (float) $destination->longitude,
                'sustainability_score' => (int) $destination->sustainability_score,
                'category' => $destination->category?->name,
                'image_url' => $destination->image_url,
            ])
            ->values()
            ->all();
    }
}
