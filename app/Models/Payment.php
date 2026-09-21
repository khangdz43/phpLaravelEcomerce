<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = ['order_id', 'provider', 'transaction_id', 'amount', 'status', 'payload', 'paid_at'];
    protected $casts = ['payload' => 'array', 'paid_at' => 'datetime', 'amount' => 'integer'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
