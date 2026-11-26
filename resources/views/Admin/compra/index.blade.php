<x-layouts.app.sidebar title="compras" >
    <flux:main>
        <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-2">
            <div class="min-w-0">
                @livewire('admin.compra-index')
            </div>
            <div class="min-w-0">
                @livewire('admin.compra-table')
            </div>
        </div>
    </flux:main>
</x-layouts.app.sidebar>