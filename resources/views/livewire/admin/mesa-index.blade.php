<div class="w-full py-8 px-4 sm:px-6 lg:px-8">
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
                customClass: { popup: 'rounded-lg shadow-lg' }
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
                customClass: { popup: 'rounded-lg shadow-lg text-left' }
            });
        </script>
    @endif

    <div class="w-full bg-zinc-900 rounded-xl shadow-2xl overflow-hidden p-6 border border-zinc-800">
        <h1 class="text-2xl font-bold text-white mb-6">Registrar Nueva Mesa</h1>

        <form action="{{ route('admin.mesas.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Número de Mesa -->
            <div>
                <label for="nro_mesas" class="block text-sm font-medium text-zinc-300 mb-1">
                    Número de Mesa <span class="text-red-500">*</span>
                </label>
                <input type="number" id="nro_mesas" name="nro_mesas" required placeholder="Ej: 1"
                    class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white">
                @error('nro_mesas')
                    <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Capacidad -->
            <div>
                <label for="capacidad" class="block text-sm font-medium text-zinc-300 mb-1">
                    Capacidad <span class="text-red-500">*</span>
                </label>
                <input type="number" id="capacidad" name="capacidad" required placeholder="Ej: 4"
                    class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white">
                @error('capacidad')
                    <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Disponibilidad -->
            <div>
                <label for="disponible" class="block text-sm font-medium text-zinc-300 mb-1">
                    ¿Disponible? <span class="text-red-500">*</span>
                </label>
                <select id="disponible" name="disponible" required
                    class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white">
                    <option value="1">Sí</option>
                    <option value="0">No</option>
                </select>
                @error('disponible')
                    <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700">
                    Registrar Mesa
                </button>
            </div>
        </form>
    </div>
</div>
