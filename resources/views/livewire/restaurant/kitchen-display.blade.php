<div wire:poll.5s class="space-y-6 p-4 md:p-6">
    @if ($errors->any())
        <div role="alert" class="rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">
            {{ $errors->first() }}
        </div>
    @endif

    @forelse ($itemsByStation as $station => $items)
        <section class="space-y-4">
            <header class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-white">
                    {{ $station === 'bar' ? 'Barra' : 'Cocina' }}
                </h2>
                <span class="rounded-full bg-zinc-800 px-3 py-1 text-sm text-zinc-300">{{ $items->count() }} productos</span>
            </header>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($items as $item)
                    @php
                        $waitMinutes = $item->created_at->diffInMinutes(now());
                        $isDelayed = $waitMinutes >= 10;
                    @endphp
                    <article wire:key="kds-item-{{ $item->id }}" class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5 shadow-lg">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-medium text-indigo-300">
                                    Orden #{{ $item->order_id }}
                                    @if ($item->order->table)
                                        · Mesa {{ $item->order->table->nro_mesas }}
                                    @else
                                        · Para llevar
                                    @endif
                                </p>
                                <h3 class="mt-2 text-lg font-semibold text-white">{{ $item->product_name }}</h3>
                            </div>
                            <span class="rounded-lg bg-zinc-800 px-3 py-2 text-lg font-bold text-white">×{{ $item->quantity }}</span>
                        </div>

                        <div class="mt-5 flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm text-zinc-300">
                                    {{ $item->status === 'pending' ? 'Pendiente' : 'En preparación' }}
                                </p>
                                <p @class([
                                    'mt-1 text-xs',
                                    'text-red-300' => $isDelayed,
                                    'text-zinc-500' => ! $isDelayed,
                                ])>
                                    Espera: {{ $waitMinutes }} min
                                    @if ($isDelayed)
                                        · Demorada
                                    @endif
                                </p>
                            </div>
                            <button type="button" wire:click="advance({{ $item->id }})" wire:loading.attr="disabled"
                                wire:target="advance({{ $item->id }})"
                                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 disabled:opacity-50">
                                {{ $item->status === 'pending' ? 'Iniciar' : 'Marcar listo' }}
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @empty
        <div class="rounded-2xl border border-dashed border-zinc-700 bg-zinc-900/50 px-6 py-16 text-center">
            <h2 class="text-lg font-semibold text-white">No hay comandas pendientes</h2>
            <p class="mt-2 text-sm text-zinc-400">Las nuevas órdenes aparecerán aquí automáticamente.</p>
        </div>
    @endforelse
</div>
