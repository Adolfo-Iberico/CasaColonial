<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Table;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderCreationService
{
    public function create(array $data, User $user): Order
    {
        return DB::transaction(function () use ($data, $user): Order {
            $quantities = collect($data['items'])
                ->filter(fn ($quantity): bool => (int) $quantity > 0);

            if ($quantities->isEmpty()) {
                throw ValidationException::withMessages([
                    'items' => 'Selecciona al menos un producto.',
                ]);
            }

            foreach ($quantities->keys() as $productId) {
                if (! ctype_digit((string) $productId)) {
                    throw ValidationException::withMessages([
                        'items' => 'La selección de productos no es válida.',
                    ]);
                }
            }

            $products = Product::query()
                ->whereIn('id', $quantities->keys())
                ->where('active', true)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($products->count() !== $quantities->count()) {
                throw ValidationException::withMessages([
                    'items' => 'Uno o más productos ya no están disponibles.',
                ]);
            }

            $tableId = null;

            if ($data['service_type'] === 'dine_in') {
                $table = Table::query()
                    ->whereKey($data['table_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $hasOpenOrder = $table->orders()
                    ->whereNotIn('status', ['completed', 'cancelled'])
                    ->exists();

                if (! $table->disponible || $hasOpenOrder) {
                    throw ValidationException::withMessages([
                        'table_id' => 'La mesa seleccionada no está disponible.',
                    ]);
                }

                $tableId = $table->id;
            }

            $lines = [];
            $totalCents = 0;

            foreach ($quantities as $productId => $quantity) {
                $product = $products->get((int) $productId);
                $unitPriceCents = (int) round((float) $product->price * 100);
                $quantity = (int) $quantity;
                $totalCents += $unitPriceCents * $quantity;
                $lines[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => number_format($unitPriceCents / 100, 2, '.', ''),
                ];
            }

            if ($totalCents > 9999999999) {
                throw ValidationException::withMessages([
                    'items' => 'El total de la orden supera el máximo permitido.',
                ]);
            }

            $order = Order::create([
                'customer_id' => $data['customer_id'] ?? null,
                'table_id' => $tableId,
                'created_by' => $user->id,
                'nombre_plato' => mb_substr(implode(', ', array_map(
                    fn (array $line): string => $line['product']->name.' x'.$line['quantity'],
                    $lines
                )), 0, 255),
                'total' => number_format($totalCents / 100, 2, '.', ''),
                'metodo_pago' => 'Pendiente',
                'service_type' => $data['service_type'],
                'status' => 'pending',
                'document_type' => $data['document_type'] ?? null,
                'customer_document' => $data['customer_document'] ?? null,
            ]);

            foreach ($lines as $line) {
                $order->items()->create([
                    'product_id' => $line['product']->id,
                    'product_name' => $line['product']->name,
                    'station' => $line['product']->station,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                ]);
            }

            return $order;
        });
    }
}
