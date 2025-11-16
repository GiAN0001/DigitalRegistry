<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\PetType;

class HouseholdPet extends Model
{
    use HasFactory; // This line will now work

    /**
     * The table associated with the model.
     * (Good practice to add this)
     * @var string
     */
    protected $table = 'household_pets';

    public function petType(): BelongsTo
    {
        return $this->belongsTo(PetType::class);
    }
}