<?php

namespace App\Http\Resources;

use App\Enums\OrderStatusEnum;
use App\Enums\OrderTrackEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderTrackResource extends JsonResource
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
            'order_id' => $this->order_id,
            'action_type' => $this->action_type?->name,
            'title' => $this->makeTitle($this->properties['new']),
            'properties' => $this->properties,
            'causer_id' => $this->causer_id,
            'date' => $this->created_at,
            'remark' => $this->remark
        ];
    }

    private function makeTitle($new_properties): string
    {
        $title = '';
        switch ($new_properties) {
            case    OrderStatusEnum::Assigned->name:
                $title = 'Your order has been Assigned';
            case OrderStatusEnum::On_Pickup_Location->name:
                $title = 'Rider have arrived at pick up location';
            case OrderStatusEnum::On_Way->name:
                $title = "Your order is on receiver's way";
            case OrderStatusEnum::On_Drop_Location->name:
                $title = "Your order is on drop location";
            case OrderStatusEnum::Delivered->name:
                $title = "Your order has been delivered";
            default:
                $title = "Your order has been " . $new_properties;
        }
        return $title;
    }
}
