<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\BarangayRole; // We need this for the function
use App\Models\Resident;
use App\Models\Household;

class User extends Authenticatable
{
    // Add the HasRoles trait from Spatie
    use HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'password',
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'contact',
        'status',
        'barangay_role_id',
        'added_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS (Computed Attributes)
    |--------------------------------------------------------------------------
    */

    /**
     * Get the user's full name.
     * This combines first_name and last_name into a single 'name' attribute.
     */
    public function getNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /*
    |--------------------------------------------------------------------------
    | MODEL RELATIONSHIPS (Our Custom Code)
    |--------------------------------------------------------------------------
    */

    /**
     * Get the real-world job title for this user.
     * (e.g., "Barangay Captain")
     */
    public function barangayRole(): BelongsTo
    {
       
        return $this->belongsTo(BarangayRole::class, 'barangay_role_id', 'id');
    }


    public function addedByUser(): BelongsTo
    {
        
        return $this->belongsTo(User::class, 'added_by', 'id');
    }

    public function updatedByUser(): BelongsTo
    {
       
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    

    
        
}