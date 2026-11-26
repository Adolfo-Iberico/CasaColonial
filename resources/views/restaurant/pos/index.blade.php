<x-layouts.app.sidebar title="POS y caja">
    <flux:main>
        <div class="space-y-6 p-4 md:p-6">
            <header class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.2em] text-indigo-400">Ventas</p>
                    <h1 class="mt-1 text-3xl font-bold text-white">Punto de venta y caja</h1>
                    <p class="mt-1 text-sm text-zinc-400">Ventas rápidas, cobros divididos y arqueo de efectivo.</p>
                </div>
                <a href="{{ route('comandas.index') }}" class="rounded-lg border border-zinc-700 px-4 py-2 text-sm text-zinc-200 hover:bg-zinc-800">
                    Abrir comandas
                </a>
            </header>

            @if (session('success'))
                <div role="status" class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div role="alert" class="rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">{{ $errors->first() }}</div>
            @endif

            @if ($lastClosedSession)
                @php
                    $methodNames = ['cash' => 'Efectivo', 'yape' => 'Yape', 'plin' => 'Plin', 'card' => 'Tarjeta', 'transfer' => 'Transferencia'];
                @endphp
                <section class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5">
                    <h2 class="font-semibold text-white">Último arqueo · {{ $lastClosedSession->closed_at->format('d/m/Y H:i') }}</h2>
                    <div class="mt-3 grid gap-3 text-sm sm:grid-cols-3">
                        <p class="text-zinc-400">Efectivo esperado: <span class="font-medium text-white">S/ {{ number_format($lastClosedSession->expected_cash, 2) }}</span></p>
                        <p class="text-zinc-400">Efectivo contado: <span class="font-medium text-white">S/ {{ number_format($lastClosedSession->counted_cash, 2) }}</span></p>
                        <p class="text-zinc-400">Diferencia:
                            <span @class([
                                'font-semibold',
                                'text-emerald-300' => (float) $lastClosedSession->difference === 0.0,
                                'text-amber-300' => (float) $lastClosedSession->difference !== 0.0,
                            ])>S/ {{ number_format($lastClosedSession->difference, 2) }}</span>
                        </p>
                    </div>
                    @if ($lastClosedSession->payments->isNotEmpty())
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($lastClosedSession->payments->groupBy('method') as $method => $payments)
                                <span class="rounded-full bg-zinc-800 px-3 py-1 text-xs text-zinc-300">
                                    {{ $methodNames[$method] ?? $method }}: S/ {{ number_format($payments->sum('amount'), 2) }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endif

            <section class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5">
                @if ($cashSession)
                    @php
                        $cashTaken = (float) $cashSession->payments->where('method', 'cash')->sum('amount');
                        $expectedCash = (float) $cashSession->opening_amount + $cashTaken;
                        $methodNames = ['cash' => 'Efectivo', 'yape' => 'Yape', 'plin' => 'Plin', 'card' => 'Tarjeta', 'transfer' => 'Transferencia'];
                    @endphp
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p class="text-sm text-emerald-300">Caja abierta desde {{ $cashSession->opened_at->format('d/m/Y H:i') }}</p>
                            <p class="mt-1 text-sm text-zinc-300">
                                Fondo inicial: S/ {{ number_format($cashSession->opening_amount, 2) }}
                                <span class="mx-1 text-zinc-600">·</span>
                                Efectivo esperado: S/ {{ number_format($expectedCash, 2) }}
                            </p>
                            @if ($cashSession->payments->isNotEmpty())
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach ($cashSession->payments->groupBy('method') as $method => $payments)
                                        <span class="rounded-full bg-zinc-800 px-3 py-1 text-xs text-zinc-300">
                                            {{ $methodNames[$method] ?? $method }}: S/ {{ number_format($payments->sum('amount'), 2) }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <form action="{{ route('pos.cash.close') }}" method="POST" class="flex flex-wrap items-end gap-2">
                            @csrf
                            <div>
                                <label for="counted_cash" class="mb-1 block text-xs text-zinc-400">Efectivo contado (S/)</label>
                                <input id="counted_cash" name="counted_cash" type="number" step="0.01" min="0" required
                                    class="w-40 rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2 text-white">
                            </div>
                            <button class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-500">Cerrar y arquear caja</button>
                        </form>
                    </div>
                @else
                    <div class="flex flex-wrap items-end justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold text-white">Caja cerrada</h2>
                            <p class="mt-1 text-sm text-zinc-400">Abre tu turno registrando el efectivo inicial.</p>
                        </div>
                        <form action="{{ route('pos.cash.open') }}" method="POST" class="flex flex-wrap items-end gap-2">
                            @csrf
                            <div>
                                <label for="opening_amount" class="mb-1 block text-xs text-zinc-400">Fondo inicial (S/)</label>
                                <input id="opening_amount" name="opening_amount" type="number" step="0.01" min="0" value="0" required
                                    class="w-40 rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2 text-white">
                            </div>
                            <button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-500">Abrir caja</button>
                        </form>
                    </div>
                @endif
            </section>

            @if ($cashSession)
                <section class="grid grid-cols-1 items-start gap-6 xl:grid-cols-2">
                    <div class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5">
                        <h2 class="text-lg font-semibold text-white">Nueva venta</h2>
                        <p class="mt-1 text-sm text-zinc-400">El total se calcula con los precios vigentes de la carta.</p>

                        @if ($products->isEmpty())
                            <p class="mt-5 rounded-xl border border-dashed border-zinc-700 p-5 text-sm text-zinc-400">
                                Primero agrega productos y precios en Carta.
                            </p>
                        @else
                            <form action="{{ route('pos.orders.store') }}" method="POST" class="mt-5 space-y-5" x-data="{ serviceType: 'dine_in', documentType: 'boleta' }">
                                @csrf
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label for="service_type" class="mb-1 block text-sm text-zinc-300">Tipo de pedido</label>
                                        <select id="service_type" name="service_type" x-model="serviceType" class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2 text-white">
                                            <option value="dine_in">En salón</option>
                                            <option value="takeaway">Para llevar</option>
                                        </select>
                                    </div>
                                    <div x-show="serviceType === 'dine_in'">
                                        <label for="table_id" class="mb-1 block text-sm text-zinc-300">Mesa</label>
                                        <select id="table_id" name="table_id" :required="serviceType === 'dine_in'" :disabled="serviceType !== 'dine_in'"
                                            class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2 text-white">
                                            <option value="">Selecciona una mesa</option>
                                            @foreach ($tables as $table)
                                                <option value="{{ $table->id }}">Mesa {{ $table->nro_mesas }} · {{ $table->capacidad }} personas</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="customer_id" class="mb-1 block text-sm text-zinc-300">Cliente (opcional)</label>
                                        <select id="customer_id" name="customer_id" class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2 text-white">
                                            <option value="">Venta de mostrador</option>
                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->name }} {{ $customer->last_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="document_type" class="mb-1 block text-sm text-zinc-300">Comprobante a registrar</label>
                                        <select id="document_type" name="document_type" x-model="documentType" class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2 text-white">
                                            <option value="boleta">Boleta</option>
                                            <option value="factura">Factura</option>
                                        </select>
                                    </div>
                                    <div x-show="documentType === 'factura'" class="sm:col-span-2">
                                        <label for="customer_document" class="mb-1 block text-sm text-zinc-300">RUC del cliente</label>
                                        <input id="customer_document" name="customer_document" :required="documentType === 'factura'"
                                            class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2 text-white">
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <h3 class="text-sm font-medium text-zinc-200">Productos</h3>
                                    <div class="max-h-80 space-y-2 overflow-y-auto pr-1">
                                        @foreach ($products as $product)
                                            <label class="flex items-center justify-between gap-3 rounded-xl border border-zinc-800 bg-zinc-950/60 p-3">
                                                <span class="min-w-0">
                                                    <span class="block truncate text-sm font-medium text-white">{{ $product->name }}</span>
                                                    <span class="text-xs text-zinc-500">{{ $product->category ?: 'Sin categoría' }} · {{ $product->station === 'bar' ? 'Barra' : 'Cocina' }}</span>
                                                </span>
                                                <span class="flex shrink-0 items-center gap-3">
                                                    <span class="text-sm text-zinc-300">S/ {{ number_format($product->price, 2) }}</span>
                                                    <input aria-label="Cantidad de {{ $product->name }}" type="number" name="items[{{ $product->id }}]" min="0" max="50" value="0"
                                                        class="w-20 rounded-lg border border-zinc-700 bg-zinc-800 px-2 py-2 text-center text-white">
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <p class="rounded-lg bg-amber-500/10 p-3 text-xs text-amber-200">
                                    El sistema registra el tipo de comprobante, pero aún no lo emite ni lo declara ante SUNAT.
                                </p>
                                <button class="w-full rounded-lg bg-indigo-600 px-4 py-3 font-medium text-white hover:bg-indigo-500">Registrar venta</button>
                            </form>
                        @endif
                    </div>

                    <div class="space-y-4">
                        <header class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-white">Órdenes activas</h2>
                            <span class="text-sm text-zinc-400">{{ $orders->count() }}</span>
                        </header>
                        @forelse ($orders as $order)
                            @php
                                $methodNames = ['cash' => 'Efectivo', 'yape' => 'Yape', 'plin' => 'Plin', 'card' => 'Tarjeta', 'transfer' => 'Transferencia'];
                            @endphp
                            <article class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <h3 class="font-semibold text-white">Orden #{{ $order->id }}</h3>
                                        <p class="mt-1 text-sm text-zinc-400">
                                            {{ $order->service_type === 'takeaway' ? 'Para llevar' : 'Salón' }}
                                            @if ($order->table)
                                                · Mesa {{ $order->table->nro_mesas }}
                                            @endif
                                            · {{ $order->statusLabel() }}
                                        </p>
                                    </div>
                                    @if ($order->document_type)
                                        <span class="rounded-full bg-zinc-800 px-3 py-1 text-xs text-zinc-300">
                                            {{ ucfirst($order->document_type) }} · sin emisión SUNAT
                                        </span>
                                    @endif
                                </div>
                                <ul class="mt-4 space-y-1 text-sm text-zinc-300">
                                    @foreach ($order->items as $item)
                                        <li class="flex justify-between gap-3"><span>{{ $item->quantity }} × {{ $item->product_name }}</span><span>S/ {{ number_format((float) $item->unit_price * $item->quantity, 2) }}</span></li>
                                    @endforeach
                                </ul>
                                <div class="mt-4 flex justify-between border-t border-zinc-800 pt-3 text-sm">
                                    <span class="text-zinc-400">Total</span>
                                    <span class="font-semibold text-white">S/ {{ number_format($order->total, 2) }}</span>
                                </div>
                                <div class="mt-1 flex justify-between text-sm">
                                    <span class="text-zinc-400">Saldo por cobrar</span>
                                    <span class="font-medium text-amber-300">S/ {{ number_format($order->balanceDue(), 2) }}</span>
                                </div>
                                @if ($order->payments->isNotEmpty())
                                    <p class="mt-2 text-xs text-zinc-500">
                                        Pagos:
                                        @foreach ($order->payments as $payment)
                                            {{ $methodNames[$payment->method] ?? $payment->method }} S/ {{ number_format($payment->amount, 2) }}@if (! $loop->last), @endif
                                        @endforeach
                                    </p>
                                @endif
                                @if ($order->balanceDue() > 0)
                                    <form action="{{ route('pos.orders.payments.store', $order) }}" method="POST" class="mt-4 grid gap-2 sm:grid-cols-[1fr_1fr_auto]">
                                        @csrf
                                        <select name="method" required class="rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2 text-sm text-white">
                                            <option value="cash">Efectivo</option>
                                            <option value="yape">Yape</option>
                                            <option value="plin">Plin</option>
                                            <option value="card">Tarjeta</option>
                                            <option value="transfer">Transferencia</option>
                                        </select>
                                        <input name="amount" type="number" min="0.01" max="{{ number_format($order->balanceDue(), 2, '.', '') }}" step="0.01"
                                            value="{{ number_format($order->balanceDue(), 2, '.', '') }}" required
                                            class="rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2 text-sm text-white">
                                        <button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-500">Cobrar</button>
                                    </form>
                                    <p class="mt-2 text-xs text-zinc-500">Para dividir el pago, registra un abono y luego agrega el siguiente medio de pago.</p>
                                @endif
                            </article>
                        @empty
                            <div class="rounded-xl border border-dashed border-zinc-700 p-8 text-center text-sm text-zinc-400">No hay órdenes pendientes.</div>
                        @endforelse
                    </div>
                </section>
            @endif
        </div>
    </flux:main>
</x-layouts.app.sidebar>
