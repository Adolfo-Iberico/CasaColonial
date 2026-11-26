<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashSession extends Model
{
    protected $fillable = [
        'opened_by',
        'closed_by',
        'opening_amount',
        'expected_cash',
        'counted_cash',
        'difference',
        'status',
        'opened_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'opening_amount' => 'decimal:2',
            'expected_cash' => 'decimal:2',
            'counted_cash' => 'decimal:2',
            'difference' => 'decimal:2',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function payments()
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function opener()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closer()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}
