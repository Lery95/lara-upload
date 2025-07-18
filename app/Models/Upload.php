<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Upload extends Model
{
    protected $fillable = ['filename', 'filepath', 'status'];

    // Default behavior (can be omitted)
    protected $keyType = 'int';
    public $incrementing = true;
}

