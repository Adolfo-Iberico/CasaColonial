<x-layouts.app.sidebar title="Proveedor">
    <flux:main>
        <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-2">
            <div class="min-w-0">
                @livewire('admin.proveedor-index')
            </div>
            <div class="min-w-0">
                @livewire('admin.proveedor-table')
            </div>
        </div>
    </flux:main>
</x-layouts.app.sidebar>