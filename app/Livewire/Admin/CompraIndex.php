<?php

namespace App\Livewire\Admin;

use App\Models\Supplier;
use App\Models\User;
use Livewire\Component;

class CompraIndex extends Component
{
    public function render()
    {
        $users = User::all();
        $suppliers = Supplier::all();
        return view('livewire.admin.compra-index', compact('users', 'suppliers'));
    }
}
