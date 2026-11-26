<div class="w-full py-8 px-4 sm:px-6 lg:px-8" x-data="tableTable()">
    <!-- Notificaciones -->
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

    <!-- Tabla de Clientes -->
    <div class="w-full bg-zinc-900 rounded-xl shadow-2xl overflow-hidden p-6 border border-zinc-800">
        <section class="mb-8 rounded-2xl border border-zinc-800 bg-gradient-to-br from-zinc-900 via-zinc-900 to-indigo-950/30 p-5 sm:p-6">
            <header class="mb-5 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-300">Vista del salón</p>
                    <h2 class="mt-1 text-xl font-bold text-white">Mapa de mesas</h2>
                    <p class="mt-1 text-sm text-zinc-400">Estado y capacidad de cada mesa en tiempo real.</p>
                </div>
                <div class="flex flex-wrap gap-2 text-xs">
                    <span class="rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-emerald-200">Libre</span>
                    <span class="rounded-full border border-rose-500/30 bg-rose-500/10 px-3 py-1 text-rose-200">Ocupada</span>
                    <span class="rounded-full border border-amber-500/30 bg-amber-500/10 px-3 py-1 text-amber-200">Por cobrar</span>
                    <span class="rounded-full border border-zinc-700 bg-zinc-800 px-3 py-1 text-zinc-300">Fuera de servicio</span>
                </div>
            </header>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5">
                @foreach ($tableMap as $table)
                    @php
                        $activeOrder = $table->orders->first();
                        $tableStatus = ! $table->disponible
                            ? 'Fuera de servicio'
                            : (! $activeOrder ? 'Libre' : ($activeOrder->status === 'served' ? 'Por cobrar' : 'Ocupada'));
                        $statusClasses = match ($tableStatus) {
                            'Libre' => 'border-emerald-500/25 bg-emerald-500/[0.04]',
                            'Ocupada' => 'border-rose-500/25 bg-rose-500/[0.04]',
                            'Por cobrar' => 'border-amber-500/25 bg-amber-500/[0.04]',
                            default => 'border-zinc-700 bg-zinc-800/40',
                        };
                        $tableTopClasses = match ($tableStatus) {
                            'Libre' => 'border-emerald-400/60 bg-emerald-500/15 text-emerald-100',
                            'Ocupada' => 'border-rose-400/60 bg-rose-500/15 text-rose-100',
                            'Por cobrar' => 'border-amber-400/60 bg-amber-500/15 text-amber-100',
                            default => 'border-zinc-600 bg-zinc-700 text-zinc-300',
                        };
                    @endphp
                    <article @class(['rounded-2xl border p-4 text-center transition hover:-translate-y-0.5 hover:shadow-lg', $statusClasses])>
                        <div class="relative mx-auto mb-3 h-16 w-16">
                            <span class="absolute left-1/2 top-0 h-2.5 w-2.5 -translate-x-1/2 rounded-full bg-zinc-500"></span>
                            <span class="absolute right-0 top-1/2 h-2.5 w-2.5 -translate-y-1/2 rounded-full bg-zinc-500"></span>
                            <span class="absolute bottom-0 left-1/2 h-2.5 w-2.5 -translate-x-1/2 rounded-full bg-zinc-500"></span>
                            <span class="absolute left-0 top-1/2 h-2.5 w-2.5 -translate-y-1/2 rounded-full bg-zinc-500"></span>
                            <div @class(['absolute inset-3 flex items-center justify-center rounded-full border-2 shadow-inner', $tableTopClasses])>
                                <span class="text-sm font-bold">{{ $table->nro_mesas }}</span>
                            </div>
                        </div>
                        <p class="text-sm font-semibold text-white">Mesa {{ $table->nro_mesas }}</p>
                        <p class="mt-1 text-xs text-zinc-400">{{ $table->capacidad }} personas</p>
                        <span @class([
                            'mt-3 inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold',
                            'bg-emerald-500/10 text-emerald-200' => $tableStatus === 'Libre',
                            'bg-rose-500/10 text-rose-200' => $tableStatus === 'Ocupada',
                            'bg-amber-500/10 text-amber-200' => $tableStatus === 'Por cobrar',
                            'bg-zinc-700 text-zinc-300' => $tableStatus === 'Fuera de servicio',
                        ])>{{ $tableStatus }}</span>
                        @if ($activeOrder)
                            <p class="mt-2 text-xs text-zinc-400">Comanda #{{ $activeOrder->id }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-white" data-flux-component="heading">
                Lista de Mesas
            </h1>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-zinc-300 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-zinc-300 uppercase">Nro de Mesas</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-zinc-300 uppercase">Capacidad</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-zinc-300 uppercase">Estado</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-zinc-300 uppercase">Servicio</th>
                        @if (auth()->user()->hasPermission('tables.update') || auth()->user()->hasPermission('tables.manage'))
                            <th class="px-4 py-3 text-right text-sm font-medium text-zinc-300 uppercase">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @foreach ($tables as $table)
                        <tr>
                            <td class="px-4 py-4 text-sm text-zinc-300">{{ $loop->iteration }}</td>
                            <td class="px-4 py-4 text-sm text-zinc-300">{{ $table->nro_mesas }}</td>
                            <td class="px-4 py-4 text-sm text-zinc-300">{{ $table->capacidad }}</td>
                            @php
                                $activeOrder = $table->orders->first();
                                $tableStatus = ! $table->disponible
                                    ? 'Fuera de servicio'
                                    : (! $activeOrder ? 'Libre' : ($activeOrder->status === 'served' ? 'Por cobrar' : 'Ocupada'));
                            @endphp
                            <td class="px-4 py-4 text-sm text-zinc-300">{{ $tableStatus }}</td>
                            <td class="px-4 py-4 text-sm text-zinc-300">
                                @if ($activeOrder)
                                    @if (auth()->user()->hasPermission('comandas.view'))
                                        <a href="{{ route('comandas.index') }}" class="text-indigo-300 hover:text-indigo-200">Orden #{{ $activeOrder->id }}</a>
                                    @else
                                        Orden #{{ $activeOrder->id }}
                                    @endif
                                @else
                                    —
                                @endif
                            </td>
                            @if (auth()->user()->hasPermission('tables.update') || auth()->user()->hasPermission('tables.manage'))
                                <td class="px-4 py-4 text-sm text-right">
                                <!-- Botón Editar -->
                                <button
                                    @click="openModal({{ $table->id }}, {{ $table->nro_mesas }}, {{ $table->capacidad }}, @js($table->disponible ? '1' : '0'))"
                                    class="text-blue-500 hover:text-blue-400 mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path
                                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </button>

                                <!-- Botón Eliminar -->
                                @if (auth()->user()->hasPermission('tables.manage'))
                                    <button onclick="confirmDelete({{ $table->id }})"
                                        class="text-red-500 hover:text-red-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    </button>

                                <!-- Formulario Eliminar (oculto) -->
                                <form id="delete-form-{{ $table->id }}"
                                    action="{{ route('admin.mesas.destroy', $table->id) }}" method="POST"
                                    class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if ($tables->hasPages())
            <div class="mt-6">
                {{ $tables->links() }}
            </div>
        @endif
    </div>

    <template x-teleport="body">
        <div x-show="isOpen" x-cloak x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Fondo oscuro -->
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-black opacity-75" @click="closeModal"></div>
                </div>

                <!-- Contenido del Modal -->
                <div
                    class="inline-block align-bottom bg-zinc-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-zinc-800">
                    <form :action="'{{ route('admin.mesas.index') }}/' + currentId" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="px-8 py-8">
                            <h3 class="text-xl font-semibold text-white mb-6">Editar Mesa</h3>

                            @if (auth()->user()->hasPermission('tables.manage'))
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-zinc-300 mb-2">Nro de mesas</label>
                                    <input type="text" x-model="currentNroMesas" name="nro_mesas"
                                        class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        required>
                                </div>

                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-zinc-300 mb-2">Capacidad</label>
                                    <input type="number" x-model="currentCapacity" name="capacidad"
                                        class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        required>
                                </div>
                            @endif

                            <!-- Campo disponibilidad -->
                            <div class="mb-6">  
                                <label class="block text-sm font-medium text-zinc-300 mb-2">Disponibilidad</label>
                                <select x-model="currentAvailability" name="disponible"
                                    class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                    <option value="1">Disponible</option>
                                    <option value="0">No disponible</option>
                                </select>
                            </div>
                        </div>

                        <div class="px-8 py-4 bg-zinc-800 flex justify-end space-x-4">
                            <button type="button" @click="closeModal" class="px-6 py-3 text-zinc-300 hover:text-white">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
    // Función para confirmar eliminación
    function confirmDelete(id) {
        Swal.fire({
            title: '¿Eliminar cliente?',
            text: "¡No podrás revertir esto!",
            icon: 'warning',
            background: '#18181b',
            color: '#f4f4f5',
            iconColor: '#ef4444',
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#6b7280',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'rounded-lg shadow-lg'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    // Componente Alpine.js para la tabla
    function tableTable() {
        return {
            isOpen: false,
            currentId: null,
            currentNroMesas: '',
            currentAvailability: '',
            currentCapacity: '',

            openModal(id, nroMesas, capacity, availability) {
                this.currentId = id;
                this.currentNroMesas = nroMesas;
                this.currentCapacity = capacity;
                this.currentAvailability = availability;
                this.isOpen = true;
                document.body.classList.add('overflow-hidden');
            },

            closeModal() {
                this.isOpen = false;
                document.body.classList.remove('overflow-hidden');
            }
        }
    }
</script>