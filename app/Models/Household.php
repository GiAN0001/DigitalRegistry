<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\AreaStreet;     
use App\Models\HouseStructure; 
use App\Models\HouseholdPet;
use App\Traits\Auditable; // GIAN ADDED THIS

use Illuminate\Database\Eloquent\SoftDeletes;

class Household extends Model
{
    protected $guarded = []; 
   
    use HasFactory, Auditable, SoftDeletes;

    //GIAN ADDED THIS
    

    public function areaStreet(): BelongsTo
    {
        return $this->belongsTo(AreaStreet::class, 'area_id'); 
    }

   
    public function houseStructure(): BelongsTo
    {
        return $this->belongsTo(HouseStructure::class, 'house_structure_id');
    }


    public function householdPets(): HasMany
    {
        return $this->hasMany(HouseholdPet::class);
    }
    
    public function residents() {
        return $this->hasMany(Resident::class); // ADDED BY GIAN
    }

    public function christmasBoxes() // added by GIAN
    {
        return $this->hasMany(ChristmasBox::class);
    }

    protected static function booted()
    {
        static::deleting(function ($household) {
            foreach ($household->residents as $resident) {
                $resident->delete();
            }
        });
        static::restoring(function ($household) {
            foreach ($household->residents()->withTrashed()->get() as $resident) {
                $resident->restore();
            }
        });
    }
}