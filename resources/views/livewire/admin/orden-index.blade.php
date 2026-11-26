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
            Registrar Nueva Orden
        </h1>
        <form action="{{ route('admin.orden.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Campo cliente -->
            <div>
                <label for="customer_id" class="block text-sm font-medium text-zinc-300 mb-1">
                    Cliente <span class="text-red-500">*</span>
                </label>
                <select id="customer_id" name="customer_id" required
                    class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-white placeholder-zinc-500">
                    <option value="">Selecciona un cliente</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
                @error('customer_id')
                    <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Campo mesa -->
            <div>
                <label for="table_id" class="block text-sm font-medium text-zinc-300 mb-1">
                    Mesa <span class="text-red-500">*</span>
                </label>
                <select id="table_id" name="table_id" required
                    class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-white placeholder-zinc-500">
                    <option value="">Selecciona una mesa</option>
                    @foreach ($tables as $table)
                        <option value="{{ $table->id }}">{{ $table->nro_mesas }}</option>
                    @endforeach
                </select>
                @error('table_id')
                    <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- <!-- Campo Total -->
            <div>
                <label for="total" class="block text-sm font-medium text-zinc-300 mb-1">
                    Total <span class="text-red-500">*</span>
                </label>
                <input type="number" id="total" name="total" required step="0.01"
                    class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-white placeholder-zinc-500"
                    placeholder="Ej: 150.00">
                @error('total')
                    <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div> --}}

            <!-- Campo nombre plato -->
            <div>
                <label for="nombre_plato" class="block text-sm font-medium text-zinc-300 mb-1">
                    Nombre del Plato <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nombre_plato" name="nombre_plato" required
                    class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-white placeholder-zinc-500">
                @error('nombre_plato')
                    <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Campo total -->
            <div>
                <label for="total" class="block text-sm font-medium text-zinc-300 mb-1">
                    Total <span class="text-red-500">*</span>
                </label>
                <input type="number" id="total" name="total" required step="0.01"
                    class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-white placeholder-zinc-500">
                @error('total')
                    <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>
            <!-- Campo metodo_pago -->
            <div>
                <label for="metodo_pago" class="block text-sm font-medium text-zinc-300 mb-1">
                    Método de Pago <span class="text-red-500">*</span>
                </label>
                <select id="metodo_pago" name="metodo_pago" required
                    class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-white placeholder-zinc-500">
                    <option value="">Selecciona un método de pago</option>
                    <option value="Efectivo">Efectivo</option>
                    <option value="Tarjeta">Tarjeta</option>
                    <option value="Transferencia">Transferencia</option>
                </select>
                @error('metodo_pago')
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
                    Registrar Compra
                </button>
            </div>
        </form>
    </div>
</div>
