<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\PetType;

class HouseholdPet extends Model
{
    use HasFactory;

    protected $table = 'household_pets'; //Modified by GIAN

    protected $guarded = []; 

    public function petType(): BelongsTo
    {
        return $this->belongsTo(PetType::class);
    }
    
}