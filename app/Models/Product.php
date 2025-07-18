<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'unique_key',
        'product_title',
        'product_description',
        'style_number',
        'mainframe_color',
        'size',
        'color_name',
        'piece_price'
    ];

    // Default behavior (can be omitted)
    protected $keyType = 'int';
    public $incrementing = true;
}

