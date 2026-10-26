<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmed extends Notification
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
        $technicianLine = $this->booking->technician
            ? "Your assigned technician is {$this->booking->technician->user->name}."
            : 'An admin will assign you an available technician shortly.';

        return (new MailMessage)
            ->subject('FixItNow: Your booking request was received')
            ->greeting("Hi {$notifiable->name},")
            ->line("We've received your request for \"{$this->booking->service_type}\".")
            ->line("Scheduled for: {$this->booking->scheduled_at->format('M d, Y g:i A')}")
            ->line("Address: {$this->booking->address}")
            ->line($technicianLine)
            ->action('View booking', route('bookings.show', $this->booking))
            ->line('Thank you for using FixItNow!');
    }
}
