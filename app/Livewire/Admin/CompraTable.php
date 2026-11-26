<?php

namespace App\Livewire\Admin;

use App\Models\Buy;
use App\Models\Supplier;
use App\Models\User;
use Livewire\Component;

class CompraTable extends Component
{
    public function render()
    {
        $buys = Buy::orderBy('created_at','desc')
            ->paginate(10);
        $users = User::all();
        $suppliers = Supplier::all();
        return view('livewire.admin.compra-table', compact('buys', 'users', 'suppliers'));
    }
}
