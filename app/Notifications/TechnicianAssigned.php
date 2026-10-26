<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TechnicianAssigned extends Notification
{
    use Queueable;

    public function __construct(
        public Booking $booking
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('FixItNow: You have a new job')
            ->greeting("Hi {$notifiable->name},")
            ->line("You've been assigned to: \"{$this->booking->service_type}\"")
            ->line("Address: {$this->booking->address}")
            ->line("Scheduled for: {$this->booking->scheduled_at->format('M d, Y g:i A')}")
            ->action('View job', route('bookings.show', $this->booking))
            ->line('Please accept or update the status once you\'re on your way.');
    }
}
