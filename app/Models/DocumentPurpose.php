<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentPurpose extends Model
{
    protected $table = 'document_purposes';

    protected $fillable = [
        'name',
    ];
}
