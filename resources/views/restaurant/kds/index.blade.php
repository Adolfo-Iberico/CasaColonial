<x-layouts.app.sidebar title="Pantalla de cocina">
    <flux:main>
        <div class="space-y-4">
            <header class="flex flex-wrap items-end justify-between gap-3 px-4 pt-5 md:px-6">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.2em] text-indigo-400">Producción</p>
                    <h1 class="mt-1 text-3xl font-bold text-white">Comandas de preparación</h1>
                </div>
                <p class="text-sm text-zinc-400">Se actualiza automáticamente cada 5 segundos.</p>
            </header>
            @livewire('restaurant.kitchen-display')
        </div>
    </flux:main>
</x-layouts.app.sidebar>
