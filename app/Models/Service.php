<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'default_price', 'category', 'is_active', 'is_quick_service'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_quick_service' => 'boolean',
    ];
}
