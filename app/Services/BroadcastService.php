<?php

namespace App\Services;

use App\Enums\MoverStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Http\Resources\RiderProfileResource;
use App\Http\Resources\SimpleMoversOrderResource;
use App\Http\Resources\SimpleOrderResource;
use App\Models\MoverOrder;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class BroadcastService
{
    protected function getReceivedChannelName($order): string
    {
        return match (get_class($order)) {
//            Order::class => 'new-order-packages-'.$order->vehicle_type_id,
//            MoverOrder::class => 'new-order-movers-'. $order->vehicle_type
            Order::class => 'new-packages-order-type-'.$order->vehicle_type_id,
            MoverOrder::class => 'new-movers-order-type-'. $order->vehicle_type_id,
        };
    }

    protected function getReceivedData($order)
    {
        return match (get_class($order)) {
            Order::class => SimpleOrderResource::make($order),
            MoverOrder::class => SimpleMoversOrderResource::make($order),
        };
    }

    protected function getAssignedChannelName($order): string
    {
        return match (get_class($order)) {
            Order::class => 'assigned-order-' . $order->id . '-' . $this->getMessageReceiverId($order->trip),
            MoverOrder::class => 'assigned-movers-order-' . $order->id . '-' . $this->getMessageReceiverId($order->trip),
        };
    }

    protected function getAssignedData($order): array
    {
        $rider = $order->trip->user
            ->loadAvg('ratings', 'rating')
            ->loadCount('trip')
            ->load(
                [
                    'vehicle' => function ($query) {
                        $query
                            ->select(['id', 'user_id', 'vehicle_type_id', 'brand_id', 'model_id', 'color_id', 'number_plate'])
                            ->with(['brand:id,value', 'model:id,value', 'color:id,value']);
                    }
                ]
            );
        return ['rider' => RiderProfileResource::make($rider)];
    }

    public function getMessage($order): string
    {
        return match (get_class($order)) {
            Order::class => $this->getPackageOrderMessage($order->status),
            MoverOrder::class => $this->getMoverOrderMessage($order->status),
            default => ''
        };
    }

    protected function getMessageReceiverId($trip): int
    {
        if ($this->isCancelledByRider($trip)) {
            return $trip->order->user_id;
        }
        return $trip->user_id;
    }

    protected function isCancelledByRider($trip): bool
    {
        if ($trip->action_by == $trip->user_id) {
            return true;
        }
        return false;
    }


    private function getPackageOrderMessage($order_status): string
    {
        return match ($order_status) {
            OrderStatusEnum::On_Pickup_Location => "Rider is on Pickup location",
            OrderStatusEnum::On_Way => "Rider is on way to deliver package",
            OrderStatusEnum::On_Drop_Location => "Rider is on Receiver's location",
            OrderStatusEnum::Delivered => "Your order has been delivered",
            OrderStatusEnum::Cancelled => "Your order has been canceled",
            default => $order_status?->name,
        };
    }

    private function getMoverOrderMessage($order_status): string
    {
        return match ($order_status) {
            MoverStatusEnum::On_Pickup_Location => "Rider is on Pickup location",
            MoverStatusEnum::On_Way => "Rider is on way to destination",
            MoverStatusEnum::On_Drop_Location => "Rider is on destination",
            MoverStatusEnum::Completed => "Your order has been completed",
            MoverStatusEnum::Cancelled => "Your order has been canceled",
            default => $order_status?->name,
        };
    }

}