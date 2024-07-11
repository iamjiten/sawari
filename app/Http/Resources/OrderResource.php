<?php

namespace App\Http\Resources;

use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use DB;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        $weight = 0;

        if (@$this->packages) {
            foreach ($this->packages as $package) {
                $weight += $package->size?->weight ?? 0;
            }
        }

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'weight' => $weight,
            'user_id' => $this->user_id,
            'user' => $this->user,
            'package_count' => $this->packages_count,
            'delivery_type_id' => $this->delivery_type_id,
            'delivery_type' => DeliveryTypeResource::make($this->deliveryType),
            'vehicle_type_id' => $this->vehicle_type_id,
            'vehicle_type' => VehicleTypeResource::make($this->vehicleType),
            'scheduled_at' => $this->scheduled_at,
            'status' => $this->status,
            'status_parsed' => $this->status?->name,
            'actual_amount' => $this->actual_amount,
            'discount_amount' => $this->discount_amount,
            'net_amount' => $this->net_amount,
            'promo_code' => $this->promo_code,
            'ratings' => SimpleRatingResource::collection($this->ratings),
            'trip' => TripResource::make($this->trip),
            'extra' => $this->extra,
            'order' => $this->route,
            'packages' => PackageResource::make($this->packages->first()),
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'expires_at' => $this->when($this->expires_at, Carbon::parse($this->expires_at)->format('Y-m-d H:i:s'), null),
        ];
    }
}
