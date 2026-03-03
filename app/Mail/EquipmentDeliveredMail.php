<?php

namespace App\Mail;

use App\Models\FacilityReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EquipmentDeliveredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public FacilityReservation $reservation,
        public string $deliveredByName,
        public string $deliveryDate
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Equipment Delivery Confirmation - Reservation #' . $this->reservation->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.equipment-delivered',
            with: [
                'reservation' => $this->reservation,
                'deliveredByName' => $this->deliveredByName,
                'deliveryDate' => $this->deliveryDate,
            ]
        );
    }
}