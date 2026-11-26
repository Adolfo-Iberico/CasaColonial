<div class="w-full py-8 px-4 sm:px-6 lg:px-8">
    {{-- Alerta --}}
    @if (session('success'))
        <script>
            Swal.fire({
                icon: "success",
                title: "¡Éxito!",
                text: "{{ session('success') }}",
                background: '#18181b',
                color: '#f4f4f5',
                iconColor: '#22c55e',
                confirmButtonColor: '#3b82f6',
                customClass: {
                    popup: 'rounded-lg shadow-lg'
                }
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: '<ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                background: '#18181b',
                color: '#f4f4f5',
                iconColor: '#ef4444',
                confirmButtonColor: '#3b82f6',
                customClass: {
                    popup: 'rounded-lg shadow-lg text-left'
                }
            });
        </script>
    @endif

    <div class="w-full bg-zinc-900 rounded-xl shadow-2xl overflow-hidden p-6 border border-zinc-800">
        <h1 class="text-2xl font-bold text-white mb-6">
            Registrar Detalle de Compra
        </h1>
        <form action="{{ route('admin.detalle.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Campo ID de Compra -->
            <div>
                <label for="buy_id" class="block text-sm font-medium text-zinc-300 mb-1">
                    ID de Compra <span class="text-red-500">*</span>
                </label>
                <select id="buy_id" name="buy_id" required
                    class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-white placeholder-zinc-500">
                    <option value="">Selecciona una compra</option>
                    @foreach ($buys as $buy)
                        <option value="{{ $buy->id }}">{{ $buy->id }} - {{ $buy->date }}</option>
                    @endforeach
                </select>
                @error('buy_id')
                    <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>
             <!-- Campo Nombre -->
            <div>
                <label for="name" class="block text-sm font-medium text-zinc-300 mb-1">
                    Nombre <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" required
                    class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-white placeholder-zinc-500"
                    placeholder="Ej: Producto A">
                @error('name')
                    <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>
            <!-- Campo Cantidad -->
            <div>
                <label for="cantidad" class="block text-sm font-medium text-zinc-300 mb-1">
                    Cantidad <span class="text-red-500">*</span>
                </label>
                <input type="number" id="cantidad" name="cantidad" required
                    class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-white placeholder-zinc-500"
                    placeholder="Ej: 10">
                @error('cantidad')
                    <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Campo Precio Unitario -->
            <div>
                <label for="precio_unitario" class="block text-sm font-medium text-zinc-300 mb-1">
                    Precio Unitario <span class="text-red-500">*</span>
                </label>
                <input type="number" id="precio_unitario" name="precio_unitario" required step="0.01"
                    class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-white placeholder-zinc-500"
                    placeholder="Ej: 15.50">
                @error('precio_unitario')
                    <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Campo Subtotal -->
            <div>
                <label for="subtotal" class="block text-sm font-medium text-zinc-300 mb-1">
                    Subtotal <span class="text-red-500">*</span>
                </label>
                <input type="number" id="subtotal" name="subtotal" required step="0.01"
                    class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-white placeholder-zinc-500"
                    placeholder="Ej: 155.00">
                @error('subtotal')
                    <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-zinc-800"></div>
                </div>
            </div>

            <!-- Nota de campos obligatorios -->
            <div class="text-sm text-zinc-500 mb-6">
                Campos marcados con <span class="text-red-500 font-bold">*</span> son obligatorios
            </div>

            <!-- Botón de acción principal -->
            <div class="flex justify-end">
                <button type="submit"
                    class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-zinc-900 transition-all duration-200 shadow-lg hover:shadow-xl">
                    Registrar Detalle de Compra
                </button>
            </div>
        </form>
    </div>
</div><script>
    document.addEventListener('DOMContentLoaded', () => {
        const cantidad = document.getElementById('cantidad');
        const precio = document.getElementById('precio_unitario');
        const subtotal = document.getElementById('subtotal');

        function calcularSubtotal() {
            const c = parseFloat(cantidad.value) || 0;
            const p = parseFloat(precio.value) || 0;
            subtotal.value = (c * p).toFixed(2);
        }

        cantidad.addEventListener('input', calcularSubtotal);
        precio.addEventListener('input', calcularSubtotal);
    });
</script>
