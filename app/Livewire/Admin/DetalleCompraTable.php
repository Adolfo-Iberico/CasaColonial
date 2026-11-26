<?php

namespace App\Livewire\Admin;

use App\Models\Buy;
use App\Models\BuyDetails;
use Livewire\Component;

class DetalleCompraTable extends Component
{
    public function render()
    {
        $details = BuyDetails::orderBy('created_at','desc')
            ->paginate(10);
        $buy = Buy::all();
        return view('livewire.admin.detalle-compra-table', compact('details', 'buy'));
    }
}
