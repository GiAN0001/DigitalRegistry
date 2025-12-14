<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $fillable = [
        'facility_type',
        'non_resident_rate',
    ];

    public function reservations()
    {
        return $this->hasMany(FacilityReservation::class);
    }
}