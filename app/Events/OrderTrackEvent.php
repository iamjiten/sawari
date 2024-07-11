<?php


namespace App\Events;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Services\BroadcastService;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderTrackEvent extends BroadcastService implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $order;
    /**
     * Create a new event instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel($this->getAssignedChannelName($this->order)),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'message' => $this->getMessage($this->order),
//            'on_location' => $this->when($this->order->status == OrderStatusEnum::On_Pickup_Location->value, true)
        ];
    }
}
