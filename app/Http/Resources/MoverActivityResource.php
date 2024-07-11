<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MoverActivityResource extends JsonResource
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
            'slug' => $this->slug,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'status_parsed' => $this->status?->name,
            'vehicle_type_id' => $this->vehicle_type_id,
            'actual_amount' => $this->actual_amount,
            'discount_amount' => $this->discount_amount,
            'net_amount' => $this->net_amount,
            'shifting_from_address' => $this->shifting_from_address,
            'shifting_from_longitude' => $this->shifting_from_longitude,
            "shifting_from_latitude" => $this->shifting_from_latitude,
            "shifting_to_address" => $this->shifting_to_address,
            "shifting_to_longitude" => $this->shifting_to_longitude,
            "shifting_to_latitude" => $this->shifting_to_latitude,
            "shifting_at" => $this->shifting_at,
            "no_of_rooms" => $this->no_of_rooms,
            "galli_distance" => $this->galli_distance,
            "no_of_loader" => $this->no_of_loader,
            "no_of_trips" => $this->no_of_trips,
            'vehicle_type' => $this->vehicleType?->only("name", "weight_capacity", "weight_unit", "icon"),
            'trip' => $this->trip,
        ];
    }
}
