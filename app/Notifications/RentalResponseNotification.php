<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RentalResponseNotification extends Notification
{
    use Queueable;

    public $order;
    public $accept_reject;
    public $description;
    /**
     * Create a new notification instance.
     */
    public function __construct($order, bool $accept_reject, $description)
    {
        $this->order = $order;
        $this->accept_reject;
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
            'title' => 'Order '. $this->order->slug,
            'body' => $this->getMessage(),
            'description' => $this->description,
            'type' => 'rental',
            'order_id' => $this->order->id,
        ];
    }

    private function getMessage(){
        if($this->accept_reject)
            return 'Your rental order has been accepted';
        return "Your rental order has been rejected";
    }
}
