<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Demographic extends Model
{
    public function getAgeAttribute(): ?int
    {
        if ($this->birthdate) {
            return Carbon::parse($this->birthdate)->age;
        }
        return null;
    }
}