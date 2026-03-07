<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use App\Models\HealthInformation; 
use App\Models\Household; 
use App\Models\HouseholdRole; 
use App\Models\Demographic; 
use App\Models\ResidencyType; 
use App\Traits\Auditable; // GIAN ADDED THIS

use Illuminate\Database\Eloquent\SoftDeletes;

class Resident extends Model
{
    use HasFactory, Auditable, SoftDeletes;
    

    //GIAN ADDED THIS
    protected $guarded = [];
    
    // --- RELATIONSHIPS ---
    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }
    
    public function householdRole(): BelongsTo
    {
        return $this->belongsTo(HouseholdRole::class);
    }

    public function demographic(): HasOne
    {
        return $this->hasOne(Demographic::class);
    }
    
    public function healthInformation(): HasOne
    {
        return $this->hasOne(HealthInformation::class);
    }

    public function residencyType(): BelongsTo
    {
        return $this->belongsTo(ResidencyType::class);
    }
    
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by_user_id');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    public function tupadParticipations()
    {
        return $this->hasMany(TupadParticipation::class);
    }

    public function allTupadParticipations()
    {
        return $this->hasManyThrough(
            TupadParticipation::class,
            Resident::class,
            'global_id',    
            'resident_id',  
            'global_id',    
            'id'            
        );
    }
    public static function getCurrentCensusCycle(): string // GIAN ADDED THIS  
    {
        $year = date('Y');
        
        // CHANGE THIS LOGIC TO REDEFINE THE CYCLE OF THE CENSUS. CURRENTLY, IT ASSUMES 2 CYCLES A YEAR (SEMESTERS).
        // Example: If you want 3 cycles a year, you would change this math.
        $semester = (date('n') <= 6) ? 1 : 2; 

        return "{$year}-{$semester}";
    }

    // --- RBAC LOGIC (SCOPE) ---
    public function scopeForUser(Builder $query, User $user): Builder
    {
        if ($user->hasRole('admin') || $user->hasRole('help desk') || $user->hasRole('super admin')) {
            return $query; 
        }

        if ($user->hasRole('staff')) {
            return $query->where('added_by_user_id', $user->id);
        }
        return $query->where('id', null); 
    }

    protected static function booted()
    {
        static::deleting(function ($resident) {
            if ($resident->demographic) {
                $resident->demographic->delete();
            }
            if ($resident->healthInformation) {
                $resident->healthInformation->delete();
            }
        });
        static::restoring(function ($resident) {
            if ($record = Demographic::withTrashed()->where('resident_id', $resident->id)->first()) {
                $record->restore();
            }
            if ($record = HealthInformation::withTrashed()->where('resident_id', $resident->id)->first()) {
                $record->restore();
            }
        });
    }
}