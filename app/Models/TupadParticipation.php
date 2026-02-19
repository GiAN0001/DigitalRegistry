<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TupadParticipation extends Model
{
    protected $fillable = [
        'resident_id',
        'status',
        'start_date',
        'end_date',
        'processed_by_user_id',
        'updated_by_user_id',
        'dropped_by_user_id', 
        'drop_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
    
    public function resident(): BelongsTo 
    {
        return $this->belongsTo(Resident::class);
    }

    public function processor(): BelongsTo 
    {
        return $this->belongsTo(User::class, 'processed_by_user_id');
    }
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id', 'id');
    }
    public function dropper(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dropped_by_user_id', 'id');
    }
}