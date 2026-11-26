<x-layouts.app :title="__('Dashboard')">
    <div class="min-h-full space-y-6 p-4 md:p-6">
        <header class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-indigo-400">Casa Colonial</p>
                <h1 class="mt-2 text-3xl font-bold text-white">Panel de control</h1>
                <p class="mt-1 text-sm text-zinc-400">Resumen general de la actividad del restaurante.</p>
            </div>
            <div class="rounded-xl border border-zinc-700 bg-zinc-900 px-4 py-3 text-sm text-zinc-300">
                {{ now()->format('d/m/Y') }}
            </div>
        </header>

        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @if (auth()->user()->hasPermission('dashboard.financials'))
                <article class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5 shadow-lg">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm text-zinc-400">Ventas del mes</p>
                            <p class="mt-3 text-3xl font-bold text-white">${{ number_format($salesThisMonth, 2) }}</p>
                            <p class="mt-2 text-xs text-zinc-500">Total de órdenes registradas este mes</p>
                        </div>
                        <span class="rounded-xl bg-emerald-500/10 p-3 text-emerald-400">
                            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7 14l4-4 4 3 6-7" />
                            </svg>
                        </span>
                    </div>
                </article>
            @endif

            <article class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5 shadow-lg">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-zinc-400">Órdenes de hoy</p>
                        <p class="mt-3 text-3xl font-bold text-white">{{ number_format($ordersToday) }}</p>
                        <p class="mt-2 text-xs text-zinc-500">Órdenes creadas durante el día</p>
                    </div>
                    <span class="rounded-xl bg-blue-500/10 p-3 text-blue-400">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                        </svg>
                    </span>
                </div>
            </article>

            <article class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5 shadow-lg">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-zinc-400">Clientes registrados</p>
                        <p class="mt-3 text-3xl font-bold text-white">{{ number_format($customerCount) }}</p>
                        <p class="mt-2 text-xs text-zinc-500">Clientes en el sistema</p>
                    </div>
                    <span class="rounded-xl bg-violet-500/10 p-3 text-violet-400">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m16 0v-2a4 4 0 0 0-3-3.87M10 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8-7.87a4 4 0 0 1 0 7.75" />
                        </svg>
                    </span>
                </div>
            </article>

            <article class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5 shadow-lg">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-zinc-400">Mesas disponibles</p>
                        <p class="mt-3 text-3xl font-bold text-white">{{ $availableTables }}<span class="text-lg font-medium text-zinc-500"> / {{ $tableCount }}</span></p>
                        <p class="mt-2 text-xs text-zinc-500">Disponibilidad actual</p>
                    </div>
                    <span class="rounded-xl bg-amber-500/10 p-3 text-amber-400">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M5 10V5h14v5m-14 0v9m14-9v9M3 19h18" />
                        </svg>
                    </span>
                </div>
            </article>
        </section>

        @if (auth()->user()->hasPermission('dashboard.financials'))
            <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <article class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5 shadow-lg xl:col-span-2">
                <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                    <div>
                        <h2 class="text-lg font-semibold text-white">Ventas y compras</h2>
                        <p class="mt-1 text-sm text-zinc-400">Comparativo de los últimos seis meses</p>
                    </div>
                    <div class="flex gap-4 text-xs text-zinc-300">
                        <span class="flex items-center gap-2"><span class="size-2.5 rounded-sm bg-indigo-500"></span>Ventas</span>
                        <span class="flex items-center gap-2"><span class="size-2.5 rounded-sm bg-emerald-500"></span>Compras</span>
                    </div>
                </div>

                <div class="mt-8 grid h-56 grid-cols-6 gap-3 border-b border-zinc-800 px-2">
                    @foreach ($monthlyData as $month)
                        <div class="flex min-w-0 flex-col items-center justify-end gap-3">
                            <div class="flex h-44 w-full items-end justify-center gap-1.5">
                                <div
                                    class="w-5 rounded-t-md bg-indigo-500 transition-all hover:bg-indigo-400"
                                    style="height: {{ $month['sales_percent'] }}%"
                                    title="Ventas: ${{ number_format($month['sales'], 2) }}"
                                ></div>
                                <div
                                    class="w-5 rounded-t-md bg-emerald-500 transition-all hover:bg-emerald-400"
                                    style="height: {{ $month['purchases_percent'] }}%"
                                    title="Compras: ${{ number_format($month['purchases'], 2) }}"
                                ></div>
                            </div>
                            <span class="pb-3 text-xs text-zinc-400">{{ $month['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5 shadow-lg">
                <h2 class="text-lg font-semibold text-white">Métodos de pago</h2>
                <p class="mt-1 text-sm text-zinc-400">Órdenes por método</p>

                @forelse ($paymentMethods as $method)
                    <div class="mt-6">
                        <div class="mb-2 flex justify-between text-sm">
                            <span class="text-zinc-300">{{ $method['name'] }}</span>
                            <span class="text-zinc-400">{{ $method['total'] }}</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-zinc-800">
                            <div class="h-full rounded-full bg-violet-500" style="width: {{ $method['percent'] }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="mt-8 rounded-xl border border-dashed border-zinc-700 p-5 text-center text-sm text-zinc-500">
                        Aún no hay órdenes registradas.
                    </p>
                @endforelse
            </article>
            </section>
        @endif

        <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <article class="overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900 shadow-lg xl:col-span-2">
                <div class="flex items-center justify-between border-b border-zinc-800 px-5 py-4">
                    <div>
                        <h2 class="text-lg font-semibold text-white">Órdenes recientes</h2>
                        <p class="mt-1 text-sm text-zinc-400">Últimos movimientos registrados</p>
                    </div>
                    @if (auth()->user()->hasPermission('orders.view'))
                        <a href="{{ route('admin.orden.index') }}" class="text-sm font-medium text-indigo-400 hover:text-indigo-300">
                            Ver órdenes
                        </a>
                    @elseif (auth()->user()->hasPermission('comandas.view'))
                        <a href="{{ route('comandas.index') }}" class="text-sm font-medium text-indigo-400 hover:text-indigo-300">
                            Ver comandas
                        </a>
                    @elseif (auth()->user()->hasPermission('kds.view'))
                        <a href="{{ route('kds.index') }}" class="text-sm font-medium text-indigo-400 hover:text-indigo-300">
                            Ver cocina
                        </a>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[640px] text-left text-sm">
                        <thead class="bg-zinc-800/60 text-xs uppercase text-zinc-400">
                            <tr>
                                <th class="px-5 py-3 font-medium">Cliente</th>
                                <th class="px-5 py-3 font-medium">Plato</th>
                                <th class="px-5 py-3 font-medium">Fecha</th>
                                @if (auth()->user()->hasPermission('dashboard.financials'))
                                    <th class="px-5 py-3 text-right font-medium">Total</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800">
                            @forelse ($recentOrders as $order)
                                <tr class="text-zinc-300">
                                    <td class="px-5 py-4">{{ $order->customer?->name ?? 'Cliente no disponible' }}</td>
                                    <td class="px-5 py-4">{{ $order->nombre_plato }}</td>
                                    <td class="px-5 py-4">{{ $order->created_at?->format('d/m/Y') ?? '—' }}</td>
                                    @if (auth()->user()->hasPermission('dashboard.financials'))
                                        <td class="px-5 py-4 text-right font-medium text-white">${{ number_format($order->total, 2) }}</td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->hasPermission('dashboard.financials') ? 4 : 3 }}" class="px-5 py-10 text-center text-zinc-500">
                                        No hay órdenes para mostrar todavía.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5 shadow-lg">
                <h2 class="text-lg font-semibold text-white">Platos más pedidos</h2>
                <p class="mt-1 text-sm text-zinc-400">Popularidad según órdenes registradas</p>

                @forelse ($popularDishes as $dish)
                    <div class="mt-6">
                        <div class="mb-2 flex justify-between gap-3 text-sm">
                            <span class="truncate text-zinc-300">{{ $dish['name'] }}</span>
                            <span class="shrink-0 text-zinc-400">{{ $dish['total'] }}</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-zinc-800">
                            <div class="h-full rounded-full bg-amber-500" style="width: {{ $dish['percent'] }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="mt-8 rounded-xl border border-dashed border-zinc-700 p-5 text-center text-sm text-zinc-500">
                        Los platos más pedidos aparecerán aquí.
                    </p>
                @endforelse
            </article>
        </section>
    </div>
</x-layouts.app>
