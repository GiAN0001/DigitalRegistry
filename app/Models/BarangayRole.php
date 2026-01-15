<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class BarangayRole extends Model
{
    use HasFactory, Auditable;
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'barangay_role_id', 'id');
    }
}
