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

class Resident extends Model
{
    use HasFactory, Auditable;
    

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
}