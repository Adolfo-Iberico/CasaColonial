<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'buy_id',
        'name',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    public function buy()
    {
        return $this->belongsTo(Buy::class,'buy_id');
    }

   
}
