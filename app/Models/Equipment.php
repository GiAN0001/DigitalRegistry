<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table = 'equipments';
    
    protected $fillable = ['equipment_type', 'total_quantity'];

    public function reservations()
    {
        return $this->belongsToMany(FacilityReservation::class, 'reservation_equipment', 'equipment_id', 'facility_reservation_id')
            ->withPivot('quantity_borrowed', 'status', 'delivered_by_name', 'received_by_user_id');
    }
}