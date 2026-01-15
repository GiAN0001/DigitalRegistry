<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class AreaStreet extends Model
{
    protected $guarded = [];
    
    use HasFactory, Auditable;
    protected $table = 'area_streets';
}
