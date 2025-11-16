<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HouseStructure extends Model
{
    use HasFactory;

    /**
     * T
     *
     * @var string
     */
    protected $table = 'house_structures';

    /**
     * The attributes that are mass assignable.
     * This allows you to create new types from an admin panel.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'house_structure_type',
    ];

    /**
     * Get all of the households that have this structure type.
     */
    public function households(): HasMany
    {
        // This is the "one-to-many" relationship.
        // One HouseStructure (e.g., "Stone") can be associated with many Households.
        return $this->hasMany(Household::class, 'house_structure_id');
    }
}