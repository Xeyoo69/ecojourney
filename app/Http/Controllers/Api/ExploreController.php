<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ExploreDestinationRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\DestinationResource;
use App\Models\Category;
use App\Models\Destination;
use App\Support\CacheKeys;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ExploreController extends Controller
{
    public function categories(): JsonResponse
    {
        $filters = $this->cachedFilters();

        return response()->json([
            'data' => $filters['categories'],
            'regions' => $filters['regions'],
        ]);
    }

    public function index(ExploreDestinationRequest $request): JsonResponse
    {
        $query = Destination::with('category');

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('region', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $query->when($request->filled('category'), function ($query) use ($request): void {
            $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $request->string('category')->toString()));
        });

        $query->when($request->filled('region'), function ($query) use ($request): void {
            $query->where('region', $request->string('region')->toString());
        });

        match ($request->input('sort', 'eco')) {
            'name' => $query->orderBy('name'),
            'latest' => $query->latest(),
            default => $query->orderByDesc('sustainability_score')->orderBy('name'),
        };

        $destinations = $query
            ->paginate($request->integer('per_page', 12))
            ->withQueryString();

        return response()->json([
            'data' => DestinationResource::collection($destinations->getCollection())->resolve(),
            'meta' => [
                'current_page' => $destinations->currentPage(),
                'from' => $destinations->firstItem(),
                'last_page' => $destinations->lastPage(),
                'per_page' => $destinations->perPage(),
                'to' => $destinations->lastItem(),
                'total' => $destinations->total(),
            ],
            'links' => [
                'first' => $destinations->url(1),
                'last' => $destinations->url($destinations->lastPage()),
                'prev' => $destinations->previousPageUrl(),
                'next' => $destinations->nextPageUrl(),
            ],
            'filters' => $this->cachedFilters(),
        ]);
    }

    /**
     * @return array{categories: array<int, array<string, mixed>>, regions: array<int, string>}
     */
    private function cachedFilters(): array
    {
        return Cache::remember(
            CacheKeys::EXPLORE_FILTERS,
            now()->addMinutes(30),
            fn (): array => [
                'categories' => CategoryResource::collection(Category::orderBy('name')->get())->resolve(),
                'regions' => Destination::query()
                    ->whereNotNull('region')
                    ->distinct()
                    ->orderBy('region')
                    ->pluck('region')
                    ->values(),
            ]
        );
    }
}
