<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipments';

    protected $fillable = [
        'equipment_type',
        'total_quantity',
    ];

    public function reservations()
    {
        return $this->belongsToMany(FacilityReservation::class, 'reservation_equipment', 'equipment_id', 'facility_reservation_id')
            ->withPivot('quantity_borrowed', 'status')
            ->withTimestamps();
    }
}