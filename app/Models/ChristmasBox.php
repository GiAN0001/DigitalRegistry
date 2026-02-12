<?php

namespace App\Models; // added by GIAN

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChristmasBox extends Model
{
    protected $fillable = [
        'household_id',
        'released_by_user_id',
        'year',
        'status',
        'date_released'
    ];

    protected $casts = [
        'date_released' => 'datetime',
    ];

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function releasedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'released_by_user_id');
    }
}