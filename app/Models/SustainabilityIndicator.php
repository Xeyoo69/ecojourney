<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SustainabilityIndicator extends Model
{
    protected $fillable = [
        'destination_id',
        'eco_transport',
        'waste_management',
        'local_community',
        'conservation',
    ];

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }
}