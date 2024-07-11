<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RiderProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vehicle_image' => $this?->vehicle?->getMedia('image')->first() ? $this?->vehicle?->getMedia('image')->first()->getUrl() : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSJdmcgDo8IzoNtOk7zTs1NumbvD4AS1qGgqbaUjZC6v7ZU483CYF1P0ROSgcJNrImYd7I&usqp=CAU',
            'name' => $this->name,
            'kyc_status' => $this->kyc_status,
            'mobile' => $this->mobile,
            'photo' => $this->photo,
            'is_online' => $this->is_online,
            'avg_rating' => round($this->ratings_avg_rating, 2),
            'number_plate' => $this?->vehicle?->number_plate,
            'brand' => $this?->vehicle?->brand?->value,
            'model' => $this?->vehicle?->model?->value,
            'color' => $this?->vehicle?->color?->value,
            'weight_capacity' => $this?->vehicle?->vehicleType?->weight_capacity,
            'weight_unit' => $this?->vehicle?->vehicleType?->weight_unit,
            'earning' => round($this->trip_sum_amount, 2),
            'trips' => $this->trip_count,
            'ratings' => $this->whenLoaded('ratings'),
            'vehicle' => $this->vehicle,
            'wallet' => $this->wallet
        ];
    }
}
