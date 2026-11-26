<?php

namespace App\Livewire\Admin;

use App\Models\Buy;
use Livewire\Component;

class DetalleCompraIndex extends Component
{
    public function render()
    {
        $buys = Buy::all();
        return view('livewire.admin.detalle-compra-index', compact('buys'));
    }
}
