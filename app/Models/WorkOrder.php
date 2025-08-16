<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class WorkOrder extends Model
{
    protected $fillable = [
        'ticket_no','customer_id','motorcycle_id','status',
        'assigned_mechanic_id','notes','started_at','completed_at'
    ];

    protected static function booted()
    {
        static::creating(function ($m) {
            $countToday = static::whereDate('created_at', now()->toDateString())->count() + 1;
            $m->ticket_no = 'MMG-'.now()->format('ymd').'-'.str_pad($countToday, 3, '0', STR_PAD_LEFT);
        });
    }

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function motorcycle(): BelongsTo { return $this->belongsTo(Motorcycle::class); }
    public function mechanic(): BelongsTo { return $this->belongsTo(User::class, 'assigned_mechanic_id'); }
    public function items(): HasMany { return $this->hasMany(WorkOrderItem::class); }
}
