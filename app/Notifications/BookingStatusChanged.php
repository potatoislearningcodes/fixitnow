<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingStatusChanged extends Notification
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
        $label = $this->booking->statusLabel();

        $mail = (new MailMessage)
            ->subject("FixItNow: Your booking is now \"{$label}\"")
            ->greeting("Hi {$notifiable->name},")
            ->line("Your request \"{$this->booking->service_type}\" is now: {$label}.");

        if ($this->booking->status === 'completed') {
            $mail->line('If payment hasn\'t been recorded yet, your technician will confirm it with you shortly.');
        }

        return $mail
            ->action('View booking', route('bookings.show', $this->booking))
            ->line('Thank you for using FixItNow!');
    }
}
