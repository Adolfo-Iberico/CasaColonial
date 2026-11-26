<x-layouts.app.sidebar title="Comandas">
    <flux:main>
        <div class="space-y-6 p-4 md:p-6">
            <header class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.2em] text-indigo-400">Salón</p>
                    <h1 class="mt-1 text-3xl font-bold text-white">Comandas y mesas</h1>
                    <p class="mt-1 text-sm text-zinc-400">Toma pedidos y sigue su avance hasta la entrega.</p>
                </div>
                @if (auth()->user()->hasPermission('pos.access'))
                    <a href="{{ route('pos.index') }}" class="rounded-lg border border-zinc-700 px-4 py-2 text-sm text-zinc-200 hover:bg-zinc-800">Ir al POS</a>
                @endif
            </header>

            @if (session('success'))
                <div role="status" class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div role="alert" class="rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">{{ $errors->first() }}</div>
            @endif

            <section class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white">Mapa de mesas</h2>
                        <p class="mt-1 text-sm text-zinc-400">Estado operativo actualizado al abrir o recargar comandas.</p>
                    </div>
                    <a href="{{ route('admin.mesas.index') }}" class="text-sm font-medium text-indigo-300 hover:text-indigo-200">Ver mesas</a>
                </div>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-7">
                    @foreach ($tableMap as $table)
                        @php
                            $activeOrder = $table->orders->first();
                            $tableStatus = ! $table->disponible
                                ? 'Fuera de servicio'
                                : (! $activeOrder ? 'Libre' : ($activeOrder->status === 'served' ? 'Por cobrar' : 'Ocupada'));
                            $tableClasses = match ($tableStatus) {
                                'Libre' => 'border-emerald-500/25 bg-emerald-500/[0.04]',
                                'Ocupada' => 'border-rose-500/25 bg-rose-500/[0.04]',
                                'Por cobrar' => 'border-amber-500/25 bg-amber-500/[0.04]',
                                default => 'border-zinc-700 bg-zinc-800/50',
                            };
                            $tableTopClasses = match ($tableStatus) {
                                'Libre' => 'border-emerald-400/60 bg-emerald-500/15 text-emerald-100',
                                'Ocupada' => 'border-rose-400/60 bg-rose-500/15 text-rose-100',
                                'Por cobrar' => 'border-amber-400/60 bg-amber-500/15 text-amber-100',
                                default => 'border-zinc-600 bg-zinc-700 text-zinc-300',
                            };
                        @endphp
                        <article @class(['rounded-2xl border p-4 text-center transition hover:-translate-y-0.5 hover:shadow-lg', $tableClasses])>
                            <div class="relative mx-auto mb-3 h-16 w-16">
                                <span class="absolute left-1/2 top-0 h-2.5 w-2.5 -translate-x-1/2 rounded-full bg-zinc-500"></span>
                                <span class="absolute right-0 top-1/2 h-2.5 w-2.5 -translate-y-1/2 rounded-full bg-zinc-500"></span>
                                <span class="absolute bottom-0 left-1/2 h-2.5 w-2.5 -translate-x-1/2 rounded-full bg-zinc-500"></span>
                                <span class="absolute left-0 top-1/2 h-2.5 w-2.5 -translate-y-1/2 rounded-full bg-zinc-500"></span>
                                <div @class(['absolute inset-3 flex items-center justify-center rounded-full border-2 shadow-inner', $tableTopClasses])>
                                    <span class="text-sm font-bold">{{ $table->nro_mesas }}</span>
                                </div>
                            </div>
                            <p class="font-semibold text-white">Mesa {{ $table->nro_mesas }}</p>
                            <p class="mt-1 text-xs text-zinc-400">{{ $table->capacidad }} personas</p>
                            <span @class([
                                'mt-3 inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold',
                                'bg-emerald-500/10 text-emerald-200' => $tableStatus === 'Libre',
                                'bg-rose-500/10 text-rose-200' => $tableStatus === 'Ocupada',
                                'bg-amber-500/10 text-amber-200' => $tableStatus === 'Por cobrar',
                                'bg-zinc-700 text-zinc-300' => $tableStatus === 'Fuera de servicio',
                            ])>{{ $tableStatus }}</span>
                            @if ($activeOrder)
                                <p class="mt-2 text-xs text-zinc-400">Comanda #{{ $activeOrder->id }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            </section>

            <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-2">
                @if (auth()->user()->hasPermission('comandas.create'))
                    <section class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5">
                        <h2 class="text-lg font-semibold text-white">Nueva comanda</h2>
                        @if ($products->isEmpty())
                            <p class="mt-4 rounded-xl border border-dashed border-zinc-700 p-5 text-sm text-zinc-400">La administración debe agregar productos a la carta antes de tomar pedidos.</p>
                        @else
                            <form action="{{ route('comandas.orders.store') }}" method="POST" class="mt-5 space-y-5" x-data="{ serviceType: 'dine_in' }">
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
                                            <option value="">Selecciona una mesa libre</option>
                                            @foreach ($tables as $table)
                                                <option value="{{ $table->id }}">Mesa {{ $table->nro_mesas }} · {{ $table->capacidad }} personas</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="customer_id" class="mb-1 block text-sm text-zinc-300">Cliente (opcional)</label>
                                        <select id="customer_id" name="customer_id" class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2 text-white">
                                            <option value="">Sin cliente registrado</option>
                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->name }} {{ $customer->last_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="max-h-80 space-y-2 overflow-y-auto pr-1">
                                    @foreach ($products as $product)
                                        <label class="flex items-center justify-between gap-3 rounded-xl border border-zinc-800 bg-zinc-950/60 p-3">
                                            <span class="min-w-0">
                                                <span class="block truncate text-sm font-medium text-white">{{ $product->name }}</span>
                                                <span class="text-xs text-zinc-500">{{ $product->station === 'bar' ? 'Barra' : 'Cocina' }} · S/ {{ number_format($product->price, 2) }}</span>
                                            </span>
                                            <input aria-label="Cantidad de {{ $product->name }}" type="number" name="items[{{ $product->id }}]" min="0" max="50" value="0"
                                                class="w-20 rounded-lg border border-zinc-700 bg-zinc-800 px-2 py-2 text-center text-white">
                                        </label>
                                    @endforeach
                                </div>
                                <button class="w-full rounded-lg bg-indigo-600 px-4 py-3 font-medium text-white hover:bg-indigo-500">Enviar comanda</button>
                            </form>
                        @endif
                    </section>
                @endif

                <section class="space-y-4">
                    <header class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-white">Pedidos en curso</h2>
                        <span class="text-sm text-zinc-400">{{ $orders->total() }}</span>
                    </header>
                    @forelse ($orders as $order)
                        <article class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-semibold text-white">Comanda #{{ $order->id }}</h3>
                                    <p class="mt-1 text-sm text-zinc-400">
                                        {{ $order->service_type === 'takeaway' ? 'Para llevar' : 'En salón' }}
                                        @if ($order->table) · Mesa {{ $order->table->nro_mesas }} @endif
                                        @if ($order->customer) · {{ $order->customer->name }} @endif
                                    </p>
                                </div>
                                <span class="rounded-full bg-indigo-500/10 px-3 py-1 text-xs font-medium text-indigo-300">{{ $order->statusLabel() }}</span>
                            </div>
                            <ul class="mt-4 space-y-2">
                                @foreach ($order->items as $item)
                                    <li class="flex items-center justify-between gap-3 text-sm">
                                        <span class="text-zinc-200">{{ $item->quantity }} × {{ $item->product_name }}</span>
                                        <span class="text-zinc-500">{{ $item->station === 'bar' ? 'Barra' : 'Cocina' }} · {{ $item->statusLabel() }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            @if (auth()->user()->hasPermission('dashboard.financials'))
                                <div class="mt-4 border-t border-zinc-800 pt-3 text-sm text-zinc-300">
                                    Total S/ {{ number_format($order->total, 2) }} · Saldo S/ {{ number_format($order->balanceDue(), 2) }}
                                </div>
                            @endif
                            @if ($order->status === 'ready' && auth()->user()->hasPermission('comandas.update'))
                                <form action="{{ route('comandas.orders.served', $order) }}" method="POST" class="mt-4">
                                    @csrf
                                    <button class="w-full rounded-lg bg-emerald-600 px-4 py-2 font-medium text-white hover:bg-emerald-500">Marcar entregada</button>
                                </form>
                            @endif
                        </article>
                    @empty
                        <div class="rounded-xl border border-dashed border-zinc-700 p-8 text-center text-sm text-zinc-400">No hay pedidos activos.</div>
                    @endforelse
                    @if ($orders->hasPages())
                        {{ $orders->links() }}
                    @endif
                </section>
            </div>
        </div>
    </flux:main>
</x-layouts.app.sidebar>
