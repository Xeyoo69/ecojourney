<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DestinationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'location' => $this->location,
            'region' => $this->region,
            'description' => $this->description,
            'image' => $this->image_url,
            'image_url' => $this->image_url,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'sustainability_score' => $this->sustainability_score,
            'rating' => [
                'average' => $this->when(
                    $this->resource->relationLoaded('reviews') || $this->resource->hasAttribute('reviews_avg_rating') || $this->resource->hasAttribute('reviews_count'),
                    function () {
                        if ($this->resource->relationLoaded('reviews')) {
                            return round((float) ($this->reviews->avg('rating') ?? 0), 1);
                        }

                        if ($this->resource->hasAttribute('reviews_avg_rating')) {
                            return round((float) $this->reviews_avg_rating, 1);
                        }

                        return 0.0;
                    }
                ),
                'count' => $this->when(
                    $this->resource->relationLoaded('reviews') || $this->resource->hasAttribute('reviews_count'),
                    function () {
                        if ($this->resource->relationLoaded('reviews')) {
                            return (int) $this->reviews->count();
                        }

                        return (int) ($this->reviews_count ?? 0);
                    }
                ),
            ],
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'activities' => ActivityResource::collection($this->whenLoaded('activities')),
            'sustainability_indicator' => SustainabilityIndicatorResource::make($this->whenLoaded('sustainabilityIndicator')),
        ];
    }
}
