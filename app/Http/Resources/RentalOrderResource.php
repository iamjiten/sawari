<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $driver_fare = @$this->extra["payment_break_down"][1]["amount"] ?? 0;
        return [
            'id' => $this->id,
            "user_id" => $this->user_id,
            "user" => $this->user?->only('name', 'mobile'),
            'slug' => $this->slug,
            'status' => $this->status,
            'status_parsed' => $this->status?->name,
            'pickup_location' => $this->pickupLocation->name,
            'pickup_date' => Carbon::parse($this->pickup_date)->toFormattedDateString() . ' ' . Carbon::parse($this->pickup_date)->format('H:i a'),
            'drop_off_location' => $this->dropOffLocation->name,
            'drop_off_date' => Carbon::parse($this->drop_off_date)->toFormattedDateString() . ' ' . Carbon::parse($this->drop_off_date)->format('H:i a'),
            'withDriver' => $this->withDriver,
            'net_amount' => $this->net_amount,
            'net_amount_withoutDriver' => $this->when($this->withDriver == 2, $this->net_amount - $driver_fare, null),
            'vehicle_id' => $this->when($this->vehicles, $this->vehicles->first()?->id),
            'vehicles' => $this->when($this->vehicles, VehicleResource::make($this->vehicles->first())),
            'merchant' => $this->when($this->vehicles, $this->vehicles->first()?->merchant),
            'extra' => $this->extra,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
