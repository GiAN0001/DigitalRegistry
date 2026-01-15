<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;

class HealthInformation extends Model
{
    use HasFactory, Auditable;
    

    //GIAN ADDED THIS


    protected $guarded = []; // Critical


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'health_informations';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'resident_id';

    /**
     * 
     *
      @var bool
     

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'resident_id',
        'sector',
        'vaccination',
        'comorbidity',
        'maintenance',
    ];

    /**
     * Get the resident that this health information belongs to.
     */
    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class, 'resident_id');
    }
}