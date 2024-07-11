<?php

namespace App\Events;

use App\Http\Resources\SimpleMoversOrderResource;
use App\Http\Resources\SimpleOrderResource;
use App\Interfaces\OrderInterface;
use App\Models\MoverOrder;
use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\Channel;

class RemoveDynamicOrderEvent  implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public OrderInterface $order;
    /**
     * Create a new event instance.
     */
    public function __construct(OrderInterface $order)
    {
        $this->order = $order;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel($this->getChannelName($this->order)),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'data' => $this->getData($this->order),
            'action' => 'remove'
        ];
    }

    /**
     * @throws \Exception
     */
    private function getChannelName($order): String
    {
        return match (get_class($order)){
            Order::class => 'new-packages-type-'.$order->vehicle_type_id,
            MoverOrder::class => 'new-movers-type-'.$order->vehicle_type_id,
            default => throw new \Exception('Unexpected order value')
        };
    }

    /**
     * @throws \Exception
     */
    private function getData($order){
        return match (get_class($order)){
            Order::class => SimpleOrderResource::make($this->order),
            MoverOrder::class => SimpleMoversOrderResource::make($this->order),
            default => throw new \Exception('Unexpected order value')
        };
    }
}
