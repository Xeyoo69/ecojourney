<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Destination extends Model
{
    protected $appends = [
        'image_url',
    ];

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'location',
        'region',
        'description',
        'image',
        'latitude',
        'longitude',
        'sustainability_score',
    ];

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
                return $this->image;
            }

            $imagePath = public_path('images/destinations/' . basename($this->image));
            if (file_exists($imagePath)) {
                return asset('images/destinations/' . basename($this->image));
            }

            return asset('storage/' . $this->image);
        }

        // fallback to logo
        return asset('images/logo-ecojourney.png');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function sustainabilityIndicator(): HasOne
    {
        return $this->hasOne(SustainabilityIndicator::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function itineraryItems(): HasMany
    {
        return $this->hasMany(ItineraryItem::class);
    }
}