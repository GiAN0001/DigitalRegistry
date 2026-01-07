<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacilityReservation extends Model
{
    protected $table = 'facility_reservations';
    
    protected $fillable = [
        'facility_id',
        'resident_type',
        'event_name',
        'resident_id',
        'renter_name',
        'renter_contact',
        'start_date',
        'end_date',
        'time_start',
        'time_end',
        'fee',
        'status',
        'processed_by_user_id',
        'mode_of_payment',
        'payment_status',
    ];

    public function equipments()
    {
        return $this->belongsToMany(Equipment::class, 'reservation_equipment', 'facility_reservation_id', 'equipment_id')
            ->withPivot('quantity_borrowed', 'status', 'delivered_by_name', 'received_by_user_id');
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by_user_id');
    }
}