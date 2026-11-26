<x-layouts.app.sidebar title="Gestión de usuarios">
    <flux:main>
        <div class="space-y-6 p-4 md:p-6">
            <header>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-indigo-400">Administración</p>
                <h1 class="mt-2 text-3xl font-bold text-white">Gestión de usuarios</h1>
                <p class="mt-1 text-sm text-zinc-400">Crea cuentas y asigna puestos con permisos adecuados a cada función.</p>
            </header>

            @if (session('success'))
                <div role="status" class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->has('user'))
                <div role="alert" class="rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                    {{ $errors->first('user') }}
                </div>
            @endif
            @if ($errors->has('role'))
                <div role="alert" class="rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                    {{ $errors->first('role') }}
                </div>
            @endif

            <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-2">
                <section class="rounded-2xl border border-zinc-800 bg-zinc-900 p-6 shadow-lg">
                    <h2 class="text-lg font-semibold text-white">Crear usuario</h2>
                    <p class="mt-1 text-sm text-zinc-400">Las cuentas nuevas se verifican para que puedan iniciar sesión inmediatamente.</p>

                    <form action="{{ route('admin.users.store') }}" method="POST" class="mt-6 space-y-5">
                        @csrf

                        <div>
                            <label for="name" class="mb-1 block text-sm font-medium text-zinc-300">Nombre</label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="255"
                                class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-white focus:border-indigo-500 focus:ring-indigo-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="mb-1 block text-sm font-medium text-zinc-300">Correo electrónico</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="255"
                                class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-white focus:border-indigo-500 focus:ring-indigo-500">
                            @error('email')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="mb-1 block text-sm font-medium text-zinc-300">Contraseña inicial</label>
                            <input id="password" name="password" type="password" required minlength="12" autocomplete="new-password"
                                class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-white focus:border-indigo-500 focus:ring-indigo-500">
                            @error('password')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="mb-1 block text-sm font-medium text-zinc-300">Confirmar contraseña</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required minlength="12" autocomplete="new-password"
                                class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-white focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="role" class="mb-1 block text-sm font-medium text-zinc-300">Puesto</label>
                            <select id="role" name="role" required
                                class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-white focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach ($roles as $value => $label)
                                    <option value="{{ $value }}" @selected(old('role', 'waiter') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="w-full rounded-lg bg-indigo-600 px-4 py-3 font-medium text-white transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-zinc-900">
                            Crear cuenta
                        </button>
                    </form>
                </section>

                <section class="overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900 shadow-lg">
                    <div class="border-b border-zinc-800 px-6 py-5">
                        <h2 class="text-lg font-semibold text-white">Usuarios registrados</h2>
                        <p class="mt-1 text-sm text-zinc-400">
                            @if ($search !== '')
                                {{ $users->total() }} {{ $users->total() === 1 ? 'resultado' : 'resultados' }} de {{ $totalUsers }} cuentas
                            @else
                                {{ $totalUsers }} cuentas en el sistema
                            @endif
                        </p>

                        <form action="{{ route('admin.users.index') }}" method="GET" class="mt-4 flex flex-col gap-2 sm:flex-row">
                            <label for="user-search" class="sr-only">Buscar usuario por nombre o correo</label>
                            <input id="user-search" name="search" type="search" value="{{ $search }}"
                                placeholder="Buscar por nombre o correo"
                                class="min-w-0 flex-1 rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-2 text-sm text-white placeholder:text-zinc-500 focus:border-indigo-500 focus:ring-indigo-500">
                            <button type="submit"
                                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                Buscar
                            </button>
                            @if ($search !== '')
                                <a href="{{ route('admin.users.index') }}"
                                    class="rounded-lg border border-zinc-700 px-4 py-2 text-center text-sm font-medium text-zinc-300 transition hover:bg-zinc-800">
                                    Limpiar
                                </a>
                            @endif
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[640px] text-left text-sm">
                            <thead class="bg-zinc-800/60 text-xs uppercase text-zinc-400">
                                <tr>
                                    <th class="px-6 py-3 font-medium">Usuario</th>
                                    <th class="px-6 py-3 font-medium">Acceso</th>
                                    <th class="px-6 py-3 font-medium">Creado</th>
                                    <th class="px-6 py-3 text-right font-medium">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-800">
                                @forelse ($users as $managedUser)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <span class="block font-medium text-white">{{ $managedUser->name }}</span>
                                            <span class="mt-1 block text-zinc-400">{{ $managedUser->email }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span @class([
                                                'rounded-full px-3 py-1 text-xs font-medium',
                                                'bg-indigo-500/10 text-indigo-300' => $managedUser->isAdmin(),
                                                'bg-zinc-700 text-zinc-300' => ! $managedUser->isAdmin(),
                                            ])>{{ $managedUser->roleLabel() }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-zinc-400">{{ $managedUser->created_at?->format('d/m/Y') ?? '—' }}</td>
                                        <td class="px-6 py-4 text-right">
                                            @if (auth()->user()->is($managedUser))
                                                <span class="text-xs text-zinc-500">Cuenta actual</span>
                                            @else
                                                <div class="flex flex-col items-end gap-3">
                                                    <form action="{{ route('admin.users.update', $managedUser) }}" method="POST"
                                                        class="flex items-center gap-2">
                                                        @csrf
                                                        @method('PUT')
                                                        <label class="sr-only" for="role-{{ $managedUser->id }}">Puesto de {{ $managedUser->name }}</label>
                                                        <select id="role-{{ $managedUser->id }}" name="role"
                                                            class="rounded-lg border border-zinc-700 bg-zinc-800 px-2 py-2 text-xs text-white">
                                                            @foreach ($roles as $value => $label)
                                                                <option value="{{ $value }}" @selected($managedUser->role === $value)>{{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button type="submit" class="font-medium text-indigo-300 hover:text-indigo-200">
                                                            Guardar
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.users.destroy', $managedUser) }}" method="POST"
                                                        onsubmit="return confirm('¿Eliminar esta cuenta?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="font-medium text-red-400 hover:text-red-300">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-zinc-500">
                                            @if ($search !== '')
                                                No se encontraron usuarios para "{{ $search }}".
                                            @else
                                                No hay usuarios registrados.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($users->hasPages())
                        <div class="border-t border-zinc-800 px-6 py-4">
                            {{ $users->links() }}
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </flux:main>
</x-layouts.app.sidebar>
