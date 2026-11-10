<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusHistory extends Model
{
    protected $fillable = ['actor_id', 'from_status', 'to_status'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return Order::STATUSES[$this->to_status] ?? $this->to_status;
    }
}
