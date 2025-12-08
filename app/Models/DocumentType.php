<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'default_fee',
    ];

    protected $casts = [
        'default_fee' => 'decimal:2',
    ];

    // Inverse relationship to DocumentRequest
    public function documentRequests(): HasMany
    {
        return $this->hasMany(DocumentRequest::class, 'document_type_id');
    }

    
}