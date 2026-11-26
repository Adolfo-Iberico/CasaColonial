<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'product_id',
        'product_name',
        'station',
        'quantity',
        'unit_price',
        'status',
        'started_at',
        'ready_at',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'started_at' => 'datetime',
            'ready_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Pendiente',
            'preparing' => 'En preparación',
            'ready' => 'Listo',
            'cancelled' => 'Anulado',
            default => 'Sin estado',
        };
    }
}
