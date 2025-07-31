<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StateCapacityLimitReached extends Notification
{
    use Queueable;

    public string $percentage;
    public string $state;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $percentage, string $state)
    {
        $this->percentage = $percentage;
        $this->state = $state;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("State Capacity Limit Reached for: {$this->state}")
            ->line("Reached the {$this->percentage} of total capacity for {$this->state}");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
