<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MoverOrderResource extends JsonResource
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
            "slug" => $this->slug,
            "user_id" => $this->user?->only('name', 'mobile'),
            'user' => $this->user,
            'status' => $this->status,
            'status_parsed' => $this->status?->name,
            "actual_amount" => $this->actual_amount,
            "discount_amount" => $this->discount_amount,
            "net_amount" => $this->net_amount,
            "vehicle_type_id" => $this->vehicle_type_id,
            'vehicle_type' => VehicleTypeResource::make($this->vehicleType),
            "shifting_from_address" => $this->shifting_from_address,
            "shifting_from_longitude" => $this->shifting_from_longitude,
            "shifting_from_latitude" => $this->shifting_from_latitude,
            "shifting_to_address" => $this->shifting_to_address,
            "shifting_to_longitude" => $this->shifting_to_longitude,
            "shifting_to_latitude" => $this->shifting_to_latitude,
            "shifting_at" => $this->shifting_at,
            "no_of_rooms" => $this->no_of_rooms,
            "galli_distance" => $this->galli_distance,
            "distance" => round($this->distance, 2),
            "no_of_loader" => $this->no_of_loader,
            "no_of_trips" => $this->no_of_trips,
            'trip' => TripResource::make($this->trip),
            "extra" => $this->extra,
            "route" => $this->route,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            "expires_at" => Carbon::parse($this->expires_at)->format('Y-m-d H:i:s'),
        ];
    }
}
