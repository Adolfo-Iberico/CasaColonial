<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'table_id',
        'created_by',
        'nombre_plato',
        'total',
        'metodo_pago',
        'service_type',
        'status',
        'document_type',
        'customer_document',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
        ];
    }
 
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function amountPaid(): float
    {
        $payments = $this->relationLoaded('payments')
            ? $this->payments
            : $this->payments()->get();

        return $payments->sum(fn (OrderPayment $payment): int => (int) round((float) $payment->amount * 100)) / 100;
    }

    public function balanceDue(): float
    {
        return max(0, ((int) round((float) $this->total * 100) - (int) round($this->amountPaid() * 100)) / 100);
    }

    public function isPaid(): bool
    {
        return $this->balanceDue() === 0.0;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Pendiente',
            'preparing' => 'En preparación',
            'ready' => 'Listo',
            'served' => 'Por cobrar',
            'completed' => 'Cerrado',
            'cancelled' => 'Anulado',
            default => 'Sin estado',
        };
    }
}
