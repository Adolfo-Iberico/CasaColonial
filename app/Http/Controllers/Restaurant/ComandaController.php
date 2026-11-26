<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRestaurantOrderRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Table;
use App\Services\OrderCreationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ComandaController extends Controller
{
    public function index(): View
    {
        return view('restaurant.comandas.index', [
            'products' => Product::query()->where('active', true)->orderBy('category')->orderBy('name')->get(),
            'customers' => Customer::query()->orderBy('name')->get(),
            'tables' => Table::query()
                ->where('disponible', true)
                ->whereDoesntHave('orders', fn ($query) => $query->whereNotIn('status', ['completed', 'cancelled']))
                ->orderBy('nro_mesas')
                ->get(),
            'tableMap' => Table::query()
                ->with(['orders' => fn ($query) => $query
                    ->whereNotIn('status', ['completed', 'cancelled'])
                    ->latest()])
                ->orderBy('nro_mesas')
                ->get(),
            'orders' => Order::query()
                ->with(['customer', 'table', 'items', 'payments'])
                ->whereIn('status', ['pending', 'preparing', 'ready', 'served'])
                ->latest()
                ->paginate(20),
        ]);
    }

    public function store(
        StoreRestaurantOrderRequest $request,
        OrderCreationService $orderCreation
    ): RedirectResponse {
        $data = $request->validated();
        unset($data['document_type'], $data['customer_document']);
        $order = $orderCreation->create($data, $request->user());

        return redirect()->route('comandas.index')->with('success', "Comanda #{$order->id} enviada a producción.");
    }

    public function markServed(Order $order): RedirectResponse
    {
        DB::transaction(function () use ($order): void {
            $lockedOrder = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($lockedOrder->status !== 'ready') {
                throw ValidationException::withMessages([
                    'order' => 'La comanda solo puede marcarse como entregada cuando todos sus productos están listos.',
                ]);
            }

            $lockedOrder->status = $lockedOrder->isPaid() ? 'completed' : 'served';
            $lockedOrder->save();
        });

        return redirect()->route('comandas.index')->with('success', "Comanda #{$order->id} marcada como entregada.");
    }
}
