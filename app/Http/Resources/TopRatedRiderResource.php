<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopRatedRiderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'avg_rating' => round($this->total_rating, 2),
            'user_id' => $this->user_id,
            'name' => $this->user->name,
            'photo' => $this->user->photo
        ];
    }
}
