<?php

namespace App\Livewire\Admin;

use App\Models\Table;
use Livewire\Component;

class MesaTable extends Component
{
    public function render()
    {
        $tableMap = Table::with(['orders' => fn ($query) => $query
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->latest()])
            ->orderBy('nro_mesas')
            ->get();

        $tables = Table::with(['orders' => fn ($query) => $query
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->latest()])
            ->orderBy('nro_mesas')
            ->paginate(10);

        return view('livewire.admin.mesa-table', compact('tableMap', 'tables'));
    }
}
