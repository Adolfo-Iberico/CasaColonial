<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    protected $fillable = [
        'cash_session_id',
        'recorded_by',
        'method',
        'amount',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function cashSession()
    {
        return $this->belongsTo(CashSession::class);
    }
}
