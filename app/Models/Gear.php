<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gear extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category',
        'price_per_day',
        'stock',
        'image_path',
        'is_featured',
    ];
}
