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
        'purpose_id',
        'other_purpose',
        'email',
        'contact_no',
        'annual_income',
        'years_of_stay',
        'months_of_stay',
        'created_by_user_id',
        'transferred_signature_by_user_id',
        'transferred_for_released_by_user_id',
        'released_by_user_id',
        'cancelled_by_user_id',
        'status',
        'for_signature_at',
        'for_release_at',
        'date_of_release',
        'date_of_cancel',
        'remarks',
        'fee',
        'update_by_user_id',
        'date_of_edited',
    ];

    protected $casts = [
        'for_signature_at' => 'datetime',
        'for_release_at' => 'datetime',
        'date_of_release' => 'datetime',
        'date_of_cancel' => 'datetime',
        'date_of_edited' => 'datetime',
        'fee' => 'decimal:2',
        'annual_income' => 'decimal:2',
    ];

    protected $appends = ['resident_name', 'date_color', 'text_color', 'border_color'];

    // Relationships
    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class, 'resident_id');
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function purpose(): BelongsTo
    {
        return $this->belongsTo(DocumentPurpose::class, 'purpose_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function transferredSignatureBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transferred_signature_by_user_id');
    }

    public function transferredForReleasedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transferred_for_released_by_user_id');
    }

    public function releasedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'released_by_user_id');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by_user_id');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'update_by_user_id');
    }

    // Scopes
    public function scopeWithNames($query)
    {
        return $query
            ->leftJoin('residents', 'document_requests.resident_id', '=', 'residents.id')
            ->leftJoin('users as creators', 'document_requests.created_by_user_id', '=', 'creators.id')
            ->leftJoin('users as signature_transferrers', 'document_requests.transferred_signature_by_user_id', '=', 'signature_transferrers.id')
            ->leftJoin('users as release_transferrers', 'document_requests.transferred_for_released_by_user_id', '=', 'release_transferrers.id')
            ->leftJoin('users as releasers', 'document_requests.released_by_user_id', '=', 'releasers.id')
            ->leftJoin('users as cancellers', 'document_requests.cancelled_by_user_id', '=', 'cancellers.id')
            ->select(
                'document_requests.*',
                DB::raw("CONCAT(residents.first_name, ' ', residents.last_name) as resident_name"),
                DB::raw("CONCAT(creators.first_name, ' ', creators.last_name) as created_by_name"),
                DB::raw("CONCAT(signature_transferrers.first_name, ' ', signature_transferrers.last_name) as transferred_signature_by_name"),
                DB::raw("CONCAT(release_transferrers.first_name, ' ', release_transferrers.last_name) as transferred_for_released_by_name"),
                DB::raw("CONCAT(releasers.first_name, ' ', releasers.last_name) as released_by_name"),
                DB::raw("CONCAT(cancellers.first_name, ' ', cancellers.last_name) as cancelled_by_name")
            );
    }

    // Accessors
    public function getResidentNameAttribute()
    {
        if (isset($this->attributes['resident_name'])) {
            return $this->attributes['resident_name'];
        }
        
        return $this->resident 
            ? $this->resident->first_name . ' ' . $this->resident->last_name 
            : 'N/A';
    }

    public function getDateColorAttribute()
    {
        return match($this->status) {
            'For Fulfillment' => 'bg-gray-200',
            'For Signature' => 'bg-blue-200',
            'For Release' => 'bg-orange-200',
            'Released' => 'bg-green-200',
            'Cancelled' => 'bg-red-200',
            default => 'bg-gray-200',
        };
    }

    public function getTextColorAttribute()
    {
        return match($this->status) {
            'For Fulfillment' => 'text-gray-700',
            'For Signature' => 'text-blue-800',
            'For Release' => 'text-orange-800',
            'Released' => 'text-green-800',
            'Cancelled' => 'text-red-700',
            default => 'text-gray-700',
        };
    }

    public function getBorderColorAttribute()
    {
        return match($this->status) {
            'For Fulfillment' => 'border-gray-500',
            'For Signature' => 'border-blue-500',
            'For Release' => 'border-orange-500',
            'Released' => 'border-green-500',
            'Cancelled' => 'border-red-500',
            default => 'border-gray-500',
        };
    }
}