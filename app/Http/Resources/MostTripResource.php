<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MostTripResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        return parent::toArray($request);

        return [
            'total_trips' => $this->total_trips,
            'user_id' => $this->user_id,
            'name' => $this->user?->name,
            'photo'=> $this->user?->photo
        ];
    }
}
