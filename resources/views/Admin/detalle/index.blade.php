<x-layouts.app.sidebar title="detalle_compra" >
    <flux:main>
        <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-2">
            <div class="min-w-0">
                @livewire('admin.detalle-compra-index')
            </div>
            <div class="min-w-0">
                @livewire('admin.detalle-compra-table')
            </div>
        </div>
    </flux:main>
</x-layouts.app.sidebar>