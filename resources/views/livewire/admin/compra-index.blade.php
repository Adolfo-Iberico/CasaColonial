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
            Registrar Nueva Compra
        </h1>
        <form action="{{ route('admin.compra.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Campo ID de Usuario -->
            <div>
                <label for="user_id" class="block text-sm font-medium text-zinc-300 mb-1">
                    ID de Usuario <span class="text-red-500">*</span>
                </label>
                <select id="user_id" name="user_id" required
                    class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-white placeholder-zinc-500">
                    <option value="">Selecciona un usuario</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Campo ID de Proveedor -->
            <div>
                <label for="supplier_id" class="block text-sm font-medium text-zinc-300 mb-1">
                    ID de Proveedor <span class="text-red-500">*</span>
                </label>
                <select id="supplier_id" name="supplier_id" 
                    class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-white placeholder-zinc-500">
                    <option value="">Selecciona un proveedor</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->razonsocial }}</option>
                    @endforeach
                </select>
                @error('supplier_id')
                    <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Campo Total -->
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
            </div>

            <!-- Campo Fecha -->
            <div>
                <label for="date" class="block text-sm font-medium text-zinc-300 mb-1">
                    Fecha <span class="text-red-500">*</span>
                </label>
                <input type="date" id="date" name="date" required
                    class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-white placeholder-zinc-500">
                @error('date')
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
