<?php

namespace App\Notifications;

use App\Enums\MoverStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Models\MoverOrder;
use App\Models\Order;
use App\Models\RentalOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OrderTrackNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;
    public $description;

    /**
     * Create a new notification instance.
     */
    public function __construct($order, $description = null)
    {
        $this->order = $order;
        $this->description = $description;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Order ' . $this->order->slug,
            'body' => $this->getMessage(get_class($this->order)),
            'description' => $this->description,
            'type' => $this->getType($this->order),
            'order_id' => $this->order->id,
        ];
    }

    private function getMessage($type): string
    {
        return match ($type) {
            Order::class => $this->getPackageOrderMessage($this->order->status),
            MoverOrder::class => $this->getMoverOrderMessage($this->order->status),
            RentalOrder::class => 'rental order',
        };
    }

    private function getPackageOrderMessage($status): string
    {
        return match ($status) {
            OrderStatusEnum::Pending => 'Your order is pending',
            OrderStatusEnum::Received => 'Your order has been created',
            OrderStatusEnum::Assigned => 'Rider has accepted your order',
            OrderStatusEnum::On_Pickup_Location => 'Rider is on your location',
            OrderStatusEnum::On_Way => 'Rider is on way to deliver your package',
            OrderStatusEnum::On_Drop_Location => 'Rider is on drop location',
            OrderStatusEnum::Delivered => 'Your package has been delivered',
            OrderStatusEnum::Cancelled => 'Your package has been cancelled',
        };
    }

    private function getMoverOrderMessage($status): string
    {
        return match ($status) {
            MoverStatusEnum::Pending => 'Your order is pending',
            MoverStatusEnum::Received => 'Your order has been created',
            MoverStatusEnum::Assigned => 'Rider has accepted your order',
            MoverStatusEnum::On_Pickup_Location => 'Rider is on your location',
            MoverStatusEnum::On_Way => 'Rider is on way',
            MoverStatusEnum::On_Drop_Location => 'Rider is on destination',
            MoverStatusEnum::Completed => 'Your order has been completed',
            MoverStatusEnum::Cancelled => 'Your order has been cancelled',
        };
    }

    private function getType($order): string
    {
        return match (get_class($order)) {
          Order::class => 'package',
          MoverOrder::class => 'mover',
          RentalOrder::class => 'rental'
        };
    }


}
