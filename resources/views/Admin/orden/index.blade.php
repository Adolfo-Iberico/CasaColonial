<x-layouts.app.sidebar title="Orden">
    <flux:main>
        @if (auth()->user()->hasPermission('orders.create'))
            <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-2">
                <div class="min-w-0">
                    @livewire('admin.orden-index')
                </div>
                <div class="min-w-0">
                    @livewire('admin.orden-table')
                </div>
            </div>
        @else
            <div class="min-w-0">
                @livewire('admin.orden-table')
            </div>
        @endif
    </flux:main>
</x-layouts.app.sidebar>