<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangayRole extends Model
{
    use HasFactory;
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'barangay_role_id', 'id');
    }
}
