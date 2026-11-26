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
            Registrar Nuevo Cliente
        </h1>
        <form action="{{ route('admin.customers.store') }}" method="POST" class="space-y-6">
            @csrf
            <!-- Campo RUC -->
            <div>
                <label for="ruc" class="block text-sm font-medium text-zinc-300 mb-1">
                    RUC <span class="text-red-500">*</span>
                </label>
                <input type="number" id="ruc" name="ruc" required
                    class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-white placeholder-zinc-500"
                    placeholder="Ej: 12345678901" data-flux-control>
                @error('ruc')
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
                    placeholder="Ej: Juan" data-flux-control>
                @error('name')
                    <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Campo Apellido -->
            <div>
                <label for="last_name" class="block text-sm font-medium text-zinc-300 mb-1">
                    Apellido <span class="text-red-500">*</span>
                </label>
                <input type="text" id="last_name" name="last_name" required
                    class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-white placeholder-zinc-500"
                    placeholder="Ej: Pérez" data-flux-control>
                @error('last_name')
                    <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Campo Teléfono -->
            <div>
                <label for="phone" class="block text-sm font-medium text-zinc-300 mb-1">
                    Teléfono <span class="text-red-500">*</span>
                </label>
                <input type="text" id="phone" name="phone" required
                    class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-white placeholder-zinc-500"
                    placeholder="Ej: 123456789" data-flux-control>
                @error('phone')
                    <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Campo Correo Electrónico -->
            <div>
                <label for="email" class="block text-sm font-medium text-zinc-300 mb-1">
                    Correo Electrónico <span class="text-red-500">*</span>
                </label>
                <input type="email" id="email" name="email" required
                    class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-white placeholder-zinc-500"
                    placeholder="Ej: ejemplo@correo.com" data-flux-control>
                @error('email')
                    <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Campo Dirección -->
            <div>
                <label for="address" class="block text-sm font-medium text-zinc-300 mb-1">
                    Dirección <span class="text-red-500">*</span>
                </label>
                <textarea id="address" name="address" rows="3" required
                    class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-white placeholder-zinc-500"
                    placeholder="Ej: Calle Falsa 123" data-flux-control></textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Campo Fecha de Registro -->
            <div>
                <label for="date_registered" class="block text-sm font-medium text-zinc-300 mb-1">
                    Fecha de Registro <span class="text-red-500">*</span>
                </label>
                <input type="date" id="date_registered" name="date_registered" required
                    class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-white placeholder-zinc-500"
                    data-flux-control>
                @error('date_registered')
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
                    Registrar Cliente
                </button>
            </div>
        </form>
    </div>
</div>