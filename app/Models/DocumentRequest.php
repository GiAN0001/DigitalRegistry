<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class DocumentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'document_type_id',
        'created_by_user_id',
        'released_by_user_id',
        'status',
        'date_of_release',
        'remarks',
        'fee',
    ];

    protected $casts = [
        'date_of_release' => 'datetime',
        'fee' => 'decimal:2',
    ];

    // Add this line to automatically append accessors
    protected $appends = ['resident_name', 'staff_name', 'releaser_name', 'date_color', 'text_color', 'border_color'];

    // Relationships
    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class, 'resident_id');
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function releasedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'released_by_user_id');
    }

    // Scopes
    /**
     * Scope to fetch with joined resident and user names (for better performance)
     */
    public function scopeWithNames($query)
    {
        return $query
            ->leftJoin('residents', 'document_requests.resident_id', '=', 'residents.id')
            ->leftJoin('users as creators', 'document_requests.created_by_user_id', '=', 'creators.id')
            ->leftJoin('users as releasers', 'document_requests.released_by_user_id', '=', 'releasers.id')
            ->select(
                'document_requests.*',
                DB::raw("CONCAT(residents.first_name, ' ', residents.last_name) as resident_name"),
                DB::raw("CONCAT(creators.first_name, ' ', creators.last_name) as staff_name"),
                DB::raw("CONCAT(releasers.first_name, ' ', releasers.last_name) as releaser_name")
            );
    }

    // Accessors
    /**
     * Get resident name (from join or relationship)
     */
    public function getResidentNameAttribute()
    {
        // If fetched via join, use that
        if (isset($this->attributes['resident_name'])) {
            return $this->attributes['resident_name'];
        }
        
        // Otherwise use relationship
        return $this->resident 
            ? $this->resident->first_name . ' ' . $this->resident->last_name 
            : 'N/A';
    }

    /**
     * Get staff name (from join or relationship)
     */
    public function getStaffNameAttribute()
    {
        // If fetched via join, use that
        if (isset($this->attributes['staff_name'])) {
            return $this->attributes['staff_name'];
        }
        
        // Otherwise use relationship
        if ($this->createdByUser) {
            return $this->createdByUser->first_name . ' ' . $this->createdByUser->last_name;
        }
        
        return 'N/A';
    }

    /**
     * Get releaser name (from join or relationship)
     */
    public function getReleaserNameAttribute()
    {
        // If fetched via join, use that
        if (isset($this->attributes['releaser_name'])) {
            return $this->attributes['releaser_name'];
        }
        
        // Otherwise use relationship
        if ($this->releasedByUser) {
            return $this->releasedByUser->first_name . ' ' . $this->releasedByUser->last_name;
        }
        
        return null;
    }

    /**
     * Get date color based on status
     */
    public function getDateColorAttribute()
    {
        return match($this->status) {
            'Pending' => 'bg-gray-200',
            'Signed' => 'bg-yellow-200',
            'Released' => 'bg-green-200',
            'Cancelled' => 'bg-red-200',
            default => 'bg-gray-200',
        };
    }

    /**
     * Get text color based on status
     */
    public function getTextColorAttribute()
    {
        return match($this->status) {
            'Pending' => 'text-gray-700',
            'Signed' => 'text-amber-800',
            'Released' => 'text-green-800',
            'Cancelled' => 'text-red-700',
            default => 'text-gray-700',
        };
    }

    /**
     * Get border color based on status
     */
    public function getBorderColorAttribute()
    {
        return match($this->status) {
            'Pending' => 'border-gray-500',
            'Signed' => 'border-amber-500',
            'Released' => 'border-green-500',
            'Cancelled' => 'border-red-500',
            default => 'border-gray-500',
        };
    }
}