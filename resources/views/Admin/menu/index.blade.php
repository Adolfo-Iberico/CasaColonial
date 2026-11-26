<x-layouts.app.sidebar title="Carta">
    <flux:main>
        <div class="space-y-6 p-4 md:p-6">
            <header>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-indigo-400">Administración</p>
                <h1 class="mt-2 text-3xl font-bold text-white">Carta de productos</h1>
                <p class="mt-1 text-sm text-zinc-400">Administra precios y la estación que recibirá cada comanda.</p>
            </header>

            @if (session('success'))
                <div role="status" class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div role="alert" class="rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">{{ $errors->first() }}</div>
            @endif

            <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-2">
                <section class="rounded-2xl border border-zinc-800 bg-zinc-900 p-6">
                    <h2 class="text-lg font-semibold text-white">Agregar producto</h2>
                    <form action="{{ route('admin.menu.store') }}" method="POST" class="mt-5 space-y-4">
                        @csrf
                        <div>
                            <label for="name" class="mb-1 block text-sm text-zinc-300">Nombre</label>
                            <input id="name" name="name" value="{{ old('name') }}" required maxlength="255"
                                class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2 text-white">
                        </div>
                        <div>
                            <label for="category" class="mb-1 block text-sm text-zinc-300">Categoría</label>
                            <input id="category" name="category" value="{{ old('category') }}" maxlength="100"
                                class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2 text-white">
                        </div>
                        <div>
                            <label for="price" class="mb-1 block text-sm text-zinc-300">Precio (S/)</label>
                            <input id="price" name="price" type="number" step="0.01" min="0.01" value="{{ old('price') }}" required
                                class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2 text-white">
                        </div>
                        <div>
                            <label for="station" class="mb-1 block text-sm text-zinc-300">Estación de preparación</label>
                            <select id="station" name="station" required class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2 text-white">
                                <option value="kitchen">Cocina</option>
                                <option value="bar">Barra</option>
                            </select>
                        </div>
                        <label class="flex items-center gap-2 text-sm text-zinc-300">
                            <input type="checkbox" name="active" value="1" checked class="rounded border-zinc-600 bg-zinc-800 text-indigo-500">
                            Disponible para venta
                        </label>
                        <button class="w-full rounded-lg bg-indigo-600 px-4 py-3 font-medium text-white hover:bg-indigo-500">Agregar a la carta</button>
                    </form>
                </section>

                <section class="space-y-4">
                    <header class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-white">Productos</h2>
                        <span class="text-sm text-zinc-400">{{ $products->total() }} registrados</span>
                    </header>
                    @forelse ($products as $product)
                        <article class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5">
                            <form action="{{ route('admin.menu.update', $product) }}" method="POST" class="space-y-3">
                                @csrf
                                @method('PUT')
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <div>
                                        <label for="name-{{ $product->id }}" class="mb-1 block text-xs text-zinc-400">Nombre</label>
                                        <input id="name-{{ $product->id }}" name="name" value="{{ $product->name }}" required
                                            class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2 text-sm text-white">
                                    </div>
                                    <div>
                                        <label for="category-{{ $product->id }}" class="mb-1 block text-xs text-zinc-400">Categoría</label>
                                        <input id="category-{{ $product->id }}" name="category" value="{{ $product->category }}"
                                            class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2 text-sm text-white">
                                    </div>
                                    <div>
                                        <label for="price-{{ $product->id }}" class="mb-1 block text-xs text-zinc-400">Precio (S/)</label>
                                        <input id="price-{{ $product->id }}" name="price" type="number" min="0.01" step="0.01" value="{{ $product->price }}" required
                                            class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2 text-sm text-white">
                                    </div>
                                    <div>
                                        <label for="station-{{ $product->id }}" class="mb-1 block text-xs text-zinc-400">Estación</label>
                                        <select id="station-{{ $product->id }}" name="station" class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2 text-sm text-white">
                                            <option value="kitchen" @selected($product->station === 'kitchen')>Cocina</option>
                                            <option value="bar" @selected($product->station === 'bar')>Barra</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <label class="flex items-center gap-2 text-sm text-zinc-300">
                                        <input type="checkbox" name="active" value="1" @checked($product->active) class="rounded border-zinc-600 bg-zinc-800 text-indigo-500">
                                        Disponible
                                    </label>
                                    <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">Guardar cambios</button>
                                </div>
                            </form>
                            <form action="{{ route('admin.menu.destroy', $product) }}" method="POST" class="mt-3 text-right"
                                onsubmit="return confirm('¿Eliminar este producto de la carta?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-sm font-medium text-red-400 hover:text-red-300">Eliminar producto</button>
                            </form>
                        </article>
                    @empty
                        <div class="rounded-xl border border-dashed border-zinc-700 p-8 text-center text-sm text-zinc-400">
                            Aún no hay productos. Agrega la carta y asígnale estación para habilitar ventas y comandas.
                        </div>
                    @endforelse
                    @if ($products->hasPages())
                        <div>{{ $products->links() }}</div>
                    @endif
                </section>
            </div>
        </div>
    </flux:main>
</x-layouts.app.sidebar>
