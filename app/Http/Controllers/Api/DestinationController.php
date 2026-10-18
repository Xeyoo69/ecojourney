<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DestinationResource;
use App\Models\Destination;
use Illuminate\Http\JsonResponse;

class DestinationController extends Controller
{
    public function show(string $slug): JsonResponse
    {
        $destination = Destination::with([
            'category',
            'activities',
            'sustainabilityIndicator',
            'reviews',
        ])->where('slug', $slug)->firstOrFail();

        $related = Destination::with('category')
            ->whereKeyNot($destination->getKey())
            ->when($destination->region, fn ($query) => $query->orderByRaw('region = ? DESC', [$destination->region]))
            ->orderByDesc('sustainability_score')
            ->orderBy('name')
            ->limit(3)
            ->get();

        return response()->json([
            'data' => [
                'destination' => DestinationResource::make($destination)->resolve(),
                'related_destinations' => DestinationResource::collection($related)->resolve(),
            ],
        ]);
    }
}
