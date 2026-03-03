<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $reason;

    public function __construct($reservation, $reason)
    {
        $this->reservation = $reservation;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->subject('Reservation Cancelled')
            ->view('emails.reservation-cancelled');
    }
}