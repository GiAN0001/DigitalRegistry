<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityReservation extends Model
{
    use HasFactory;

    protected $table = 'facility_reservations';
    //Modified by Cath
    protected $fillable = [
        'facility_id',
        'event_name',
        'purpose_category',
        'resident_type',
        'resident_id',
        'renter_name',
        'renter_contact',
        'email',
        'start_date',
        'end_date',
        'time_start',
        'time_end',
        'status',
        'processed_by_user_id',
        'transferred_for_payment_by_user_id',
        'transferred_paid_by_user_id',
        'cancelled_by_user_id',
        'rejected_by_user_id',
        'for_payment_at',
        'paid_at',
        'date_of_cancelled',
        'date_of_rejected',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'time_start' => 'datetime',
        'time_end' => 'datetime',
        'for_payment_at' => 'datetime',
        'paid_at' => 'datetime',
        'date_of_cancelled' => 'datetime',
        'date_of_rejected' => 'datetime',
    ];

    // Relationships
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

    public function transferredForPaymentBy()
    {
        return $this->belongsTo(User::class, 'transferred_for_payment_by_user_id');
    }

    public function transferredPaidBy()
    {
        return $this->belongsTo(User::class, 'transferred_paid_by_user_id');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by_user_id');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by_user_id');
    }
} 
//-----------------------