<?php

namespace App\Notifications;

use App\Models\Citizenship;
use App\Models\DrivingLicense;
use App\Models\Vehicle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KycVerificationNotification extends Notification
{
    use Queueable;

    public $kyc;
    public $review_status;

    /**
     * Create a new notification instance.
     */
    public function __construct($kyc, bool $review_status)
    {
        $this->kyc = $kyc;
        $this->review_status = $review_status;
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
            'title' => 'Kyc ' . $this->review_status ? 'verified' : 'verification failed',
            'body' => $this->getMessage(),
            'description' => $this->kyc->review,
            'type' => $this->getType($this->kyc),
            'order_id' => null
        ];
    }

    private function getMessage(): string
    {
        if ($this->review_status)
            return 'Your Kyc have been verified';
        return match (get_class($this->kyc)) {
            Citizenship::class => 'Citizenship verification failed',
            DrivingLicense::class => 'Driving License verification failed',
            Vehicle::class => 'Vehicle verification failed'
        };
    }

    public function getType($kyc)
    {
        return match (get_class($kyc)){
          Citizenship::class => 'citizenship',
          DrivingLicense::class => 'license',
          Vehicle::class => 'vehicle'
        };
    }
}
