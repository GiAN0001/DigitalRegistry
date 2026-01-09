<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaStreet extends Model
{
    protected $guarded = [];
    
    use HasFactory;
    protected $table = 'area_streets';
}
