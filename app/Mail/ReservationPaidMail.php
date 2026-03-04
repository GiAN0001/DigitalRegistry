<?php

namespace App\Mail;

use App\Models\FacilityReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationPaidMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $amountPaid;
    public $modeOfPayment;
    public $orNumber;

    public function __construct(FacilityReservation $reservation, $amountPaid, $modeOfPayment, $orNumber)
    {
        $this->reservation = $reservation;
        $this->amountPaid = $amountPaid;
        $this->modeOfPayment = $modeOfPayment;
        $this->orNumber = $orNumber;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Facility Reservation Payment Confirmation - ' . $this->reservation->event_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation-paid',
            with: [
                'reservation' => $this->reservation,
                'amountPaid' => $this->amountPaid,
                'modeOfPayment' => $this->modeOfPayment,
                'orNumber' => $this->orNumber,
            ],
        );
    }
}