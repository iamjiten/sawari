<?php

namespace App\Http\Resources;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class OnTripRiderResource extends JsonResource
{
    public function toArray($request): array
    {
        $trip = $this?->trip()?->latest()?->first();
        if ($trip?->order_type == get_class(new Order())) {
            $from = $trip?->order?->packages?->first()?->sender_address;
            $to = $trip?->order?->packages?->first()?->receiver_address;
        } else {
            $from = $trip?->order?->shifting_from_address;
            $to = $trip?->order?->shifting_to_address;
        }
        return [
            'id' => $this->id,
            'type' => $trip?->order_type == get_class(new Order()) ? 'Packages' : 'Movers',
            'name' => $this->name,
            'customer' => $trip?->order?->user,
            'mobile' => $this->mobile,
            'vehicle_type' => $trip?->order?->vehicleType?->name,
            'net_amount' => $trip->amount,
            'from' => $from,
            'to' => $to,
            'image' => $this->getMedia('profile')->first() ? $this->getMedia('profile')->first()->getUrl() : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT8lCCz3naP8eDnFr476DeuRV8FvxmratoD6q-yuxjKsMu-0DQCKMzH5PeTAk6qB7UyxmE&usqp=CAU',
        ];
    }
}
