<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Demographic extends Model
{

    //GIAN ADDED THIS
    protected $guarded = []; // Critical
    public $timestamps = false; // Usually demographic tables don't have created_at/updated_at
    protected $primaryKey = 'resident_id';
    public $incrementing = false;
    
    public function getAgeAttribute(): ?int
    {
        if ($this->birthdate) {
            return Carbon::parse($this->birthdate)->age;
        }
        return null;
    }
}