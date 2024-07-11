<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ReceiverActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $order = $this->packages
                    ->filter(function ($package) {
                        return ($package->receiver_id == auth()->id() && $package->is_receiver_user == 1);
                    });

        if($order->isEmpty()){
            return [];
        }

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'delivery_type' => $this->deliveryType->name,
            'vehicle_type' => $this->vehicleType->name,
            'status' => $this->status,
            'status_parsed' => $this->status?->name,
            'actual_amount' => $this->actual_amount,
            'discount_amount' => $this->discount_amount,
            'net_amount' => $this->net_amount,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'extra' => $this->extra,
            'packages' => $this->packages
        ];
    }
}
