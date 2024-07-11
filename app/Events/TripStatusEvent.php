<?php


namespace App\Events;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\Trip;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TripStatusEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private Trip $trip;

    /**
     * Create a new event instance.
     */
    public function __construct($trip)
    {
        $this->trip = $trip;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('assigned-order-' . $this->trip->order_id . '-' . $this->getMessageReceiverId())
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'message' => $this->getMessage(),
            'reason' => $this->trip->reason->value,
        ];
    }

    private function getMessage(): string
    {
       if($this->isCancelledByRider()){
           return "Rider has canceled your order";
       }
       return "Sender has canceled your order";
    }

    private function getMessageReceiverId(): int
    {
        if ($this->isCancelledByRider()) {
            return $this->trip->order->user_id;
        }
        return $this->trip->user_id;
    }

    private function isCancelledByRider(): bool
    {
        if ($this->trip->action_by == $this->trip->user_id) {
            return true;
        }
        return false;
    }
}
