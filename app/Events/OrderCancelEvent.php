<?php

namespace App\Events;

use App\Services\BroadcastService;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCancelEvent extends BroadcastService implements ShouldBroadcast
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
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel( $this->getAssignedChannelName($this->order))
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'message' => $this->getCancelMessage(),
            'reason' => $this->order->trip->reason->value,
        ];
    }

    private function getCancelMessage(): string
    {
        if($this->isCancelledByRider($this->order->trip)){
            return "Rider has canceled your order";
        }
        return "Sender has canceled your order";
    }
}
