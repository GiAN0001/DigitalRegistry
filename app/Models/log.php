<?php

namespace App\Models;

use App\Enums\LogAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends Model
{
    protected $table = 'logs'; // Points to your 'logs' table
    public $timestamps = false; // Custom 'date' column is used instead of created_at

    protected $fillable = [
        'user_id',
        'log_type',
        'description',
        'date'
    ];

    /**
     * FORENSIC FIX: Cast 'date' to datetime so ->format() works in Blade.
     * This matches the style used in your User.php model.
     */
    protected function casts(): array
    {
        return [
            'log_type' => LogAction::class,
            'date' => 'datetime', // This converts the string to a Carbon object
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}