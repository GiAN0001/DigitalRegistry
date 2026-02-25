<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationForPaymentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $fee;
    
    public function __construct($reservation, $fee)
    {
        $this->reservation = $reservation;
        $this->fee = $fee;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reservation Approved - Payment Required',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation-for-payment',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
