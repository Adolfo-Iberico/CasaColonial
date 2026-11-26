<?php

namespace App\Livewire\Admin;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Table;
use Livewire\Component;

class OrdenIndex extends Component
{
    public function render()
    {
        $orders = Order::all();
        $customers = Customer::all(); // Example of fetching customers
        $tables = Table::all(); // Example of fetching tables
        return view('livewire.admin.orden-index', compact('orders', 'customers', 'tables'));
    }
}
