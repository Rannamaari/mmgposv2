<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Part extends Model
{
    protected $fillable = ['sku', 'name', 'price', 'cost', 'stock_qty', 'is_active', 'is_quick_service'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_quick_service' => 'boolean',
    ];

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
