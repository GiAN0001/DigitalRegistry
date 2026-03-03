<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\FacilityReservation;

class ReservationRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $reason;

    public function __construct(FacilityReservation $reservation, $reason)
    {
        $this->reservation = $reservation;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->subject('Reservation Rejected')
            ->view('emails.reservation-rejected');
    }
}