<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DestinationResource;
use App\Models\Destination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $destinations = $request->user()
            ->favoriteDestinations()
            ->with('category')
            ->orderByPivot('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => DestinationResource::collection($destinations)->resolve(),
            'slugs' => $destinations->pluck('slug')->values()->all(),
        ]);
    }

    public function store(Request $request, string $slug): JsonResponse
    {
        $destination = Destination::where('slug', $slug)->firstOrFail();

        $request->user()->favoriteDestinations()->syncWithoutDetaching([$destination->id]);

        return response()->json([
            'message' => 'Destination saved to your favorites.',
            'data' => DestinationResource::make($destination)->resolve(),
        ], 201);
    }

    public function destroy(Request $request, string $slug): JsonResponse
    {
        $destination = Destination::where('slug', $slug)->firstOrFail();

        $request->user()->favoriteDestinations()->detach($destination->id);

        return response()->json([
            'message' => 'Destination removed from your favorites.',
        ]);
    }

    public function clear(Request $request): JsonResponse
    {
        $request->user()->favoriteDestinations()->detach();

        return response()->json([
            'message' => 'Favorites cleared.',
        ]);
    }
}
