<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(string $slug): JsonResponse
    {
        $destination = Destination::where('slug', $slug)->firstOrFail();

        $reviews = $destination->reviews()
            ->with('user:id,name')
            ->latest()
            ->latest('id')
            ->paginate(50)
            ->through(fn (Review $review): array => [
                'id' => $review->id,
                'rating' => (int) $review->rating,
                'title' => $review->title,
                'comment' => $review->comment,
                'user' => [
                    'id' => $review->user?->id,
                    'name' => $review->user?->name,
                ],
                'created_at' => $review->created_at?->toIso8601String(),
            ]);

        return response()->json($reviews);
    }

    public function store(Request $request, string $slug): JsonResponse
    {
        $destination = Destination::where('slug', $slug)->firstOrFail();
        $user = $request->user();

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:120'],
            'comment' => ['required', 'string', 'min:10', 'max:1500'],
        ]);

        $review = $destination->reviews()->updateOrCreate(
            ['user_id' => $user->getKey()],
            $validated + ['user_id' => $user->getKey()]
        );

        return response()->json([
            'data' => [
                'id' => $review->id,
                'rating' => (int) $review->rating,
                'title' => $review->title,
                'comment' => $review->comment,
                'user' => ['id' => $user->getKey(), 'name' => $user->name],
                'created_at' => $review->created_at?->toIso8601String(),
            ],
        ], 201);
    }

    public function destroy(Request $request, string $slug, int $id): JsonResponse
    {
        $destination = Destination::where('slug', $slug)->firstOrFail();

        $review = Review::where('destination_id', $destination->id)
            ->where('user_id', $request->user()->getKey())
            ->findOrFail($id);

        $review->delete();

        return response()->json(status: 204);
    }
}
