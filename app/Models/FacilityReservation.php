<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityReservation extends Model
{
    use HasFactory;

    protected $table = 'facility_reservations';
    
    protected $fillable = [
        'facility_id',
        'event_name',
        'processed_by_user_id',
        'resident_id',
        'resident_type',
        'renter_name',
        'renter_contact',
        'time_start',
        'time_end',
        'start_date',
        'end_date',
        'status',
        'mode_of_payment',
        'payment_status',
        'fee',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'fee' => 'decimal:2',
    ];

    public function equipments()
    {
        return $this->belongsToMany(Equipment::class, 'reservation_equipment', 'facility_reservation_id', 'equipment_id')
            ->withPivot('quantity_borrowed', 'status')
            ->withTimestamps();
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