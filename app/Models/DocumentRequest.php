<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    // Helper method to get resident name (from join or relationship)
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

    // Helper method to get staff name (from join or relationship)
    public function getStaffNameAttribute()
    {
        // If fetched via join, use that
        if (isset($this->attributes['staff_name'])) {
            return $this->attributes['staff_name'];
        }
        
        // Otherwise use relationship
        return $this->createdByUser?->name ?? 'N/A';
    }

    // Helper methods for status colors (background)
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

    // Helper method for text color
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

    // Helper method for border color
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