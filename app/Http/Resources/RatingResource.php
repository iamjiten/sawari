<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RatingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "order_id" => $this->order_id,
            "user_id" => $this->user_id,
            "trip_id" => $this->trip_id,
            "rating" => $this->rating,
            "review" => $this->review,
            "rated_by" => $this->rated_by,
            "extra" => $this->extra
        ];
    }
}
