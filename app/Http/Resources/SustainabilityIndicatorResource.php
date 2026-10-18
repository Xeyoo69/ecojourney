<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SustainabilityIndicatorResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'eco_transport' => $this->eco_transport,
            'waste_management' => $this->waste_management,
            'local_community' => $this->local_community,
            'conservation' => $this->conservation,
        ];
    }
}
