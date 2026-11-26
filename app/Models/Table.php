<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;
    
    protected $fillable = ['nro_mesas', 'capacidad', 'disponible'];

    protected $casts = [
        'disponible' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
