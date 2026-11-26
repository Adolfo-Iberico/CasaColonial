<?php

namespace App\Livewire\Admin;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Table;
use Livewire\Component;

class OrdenTable extends Component
{
    public function render()
    {
        $orders = Order::orderBy('created_at', 'desc')
            ->paginate(10);
        $table = Table::all();
        $customer = Customer::all(); // Example of fetching customers
        return view('livewire.admin.orden-table', compact('orders', 'table', 'customer'));
    }
}
