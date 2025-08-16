<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    protected $fillable = ['part_id', 'change_qty', 'reason', 'ref_type', 'ref_id'];

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class);
    }
}
