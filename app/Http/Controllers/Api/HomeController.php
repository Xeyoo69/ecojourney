<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DestinationResource;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Setting;
use App\Support\CacheKeys;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $payload = Cache::remember(
            CacheKeys::HOME_PAYLOAD,
            now()->addMinutes(15),
            fn (): array => $this->buildPayload()
        );

        return response()->json(['data' => $payload]);
    }

    private function buildPayload(): array
    {
        $stats = [
            'destinations' => Destination::count(),
            'categories' => Category::count(),
            'regions' => Destination::query()
                ->whereNotNull('region')
                ->distinct()
                ->count('region'),
        ];

        $featured = DestinationResource::collection(
            Destination::with('category')
                ->orderByDesc('sustainability_score')
                ->orderBy('name')
                ->limit(6)
                ->get()
        )->resolve();

        $heroImage = Setting::get('hero_image');

        return [
            'stats' => $stats,
            'featured_destinations' => $featured,
            'hero_image_url' => $heroImage !== null ? asset('storage/' . $heroImage) : null,
        ];
    }
}
