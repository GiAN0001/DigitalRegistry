<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class ResidencyType extends Model
{
    use HasFactory, Auditable;


    protected $table = 'residency_types';
}