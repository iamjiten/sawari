<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalActivityResource extends JsonResource
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
            'slug' => $this->slug,
            "user_id" => $this->user_id,
            "status" => $this->status,
            "status_parsed" => $this->status?->name,
            "actual_amount" => $this->actual_amount,
            "discount_amount" => $this->discount_amount,
            "net_amount" => $this->net_amount,
            "pickup_date" => $this->pickup_date,
            "drop_off_date" => $this->drop_off_date,
            "withDriver" => $this->withDriver,
            "extra" => $this->extra,
            "remarks" => $this->remarks,
            "transaction" => SimpleTransactionResource::make($this->transaction),
            "pickup_location" => $this->pickupLocation->name,
            "drop_off_location" => $this->dropoffLocation->name,
            "vehicle" => SimpleVehicleResource::make($this->vehicles->first())
        ];
    }
}
