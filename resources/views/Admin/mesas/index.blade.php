<x-layouts.app.sidebar title="Mesa">
    <flux:main>
        @if (auth()->user()->hasPermission('tables.manage'))
            <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-2">
                <div class="min-w-0">
                    @livewire('admin.mesa-index')
                </div>
                <div class="min-w-0">
                    @livewire('admin.mesa-table')
                </div>
            </div>
        @else
            <div class="min-w-0">
                @livewire('admin.mesa-table')
            </div>
        @endif
    </flux:main>
</x-layouts.app.sidebar>