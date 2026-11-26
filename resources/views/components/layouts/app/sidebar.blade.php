<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <div class="mx-3 mb-4 rounded-xl border border-indigo-500/20 bg-indigo-500/10 px-3 py-2">
                <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-indigo-300">{{ __('Rol activo') }}</p>
                <p class="mt-1 truncate text-sm font-medium text-white">{{ auth()->user()->roleLabel() }}</p>
            </div>

            <flux:navlist variant="outline">
                @if (auth()->user()->hasPermission('dashboard.view'))
                    <flux:navlist.group :heading="__('Inicio')" class="grid">
                        <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                    </flux:navlist.group>
                @endif

                @if (auth()->user()->hasPermission('pos.access') || auth()->user()->hasPermission('comandas.view') || auth()->user()->hasPermission('customers.view') || auth()->user()->hasPermission('tables.view'))
                    <flux:navlist.group :heading="__('Salón y atención')" class="grid">
                        @if (auth()->user()->hasPermission('pos.access'))
                            <flux:navlist.item icon="shopping-bag" :href="route('pos.index')" :current="request()->routeIs('pos.*')" wire:navigate>{{ __('POS y Caja') }}</flux:navlist.item>
                        @endif
                        @if (auth()->user()->hasPermission('comandas.view'))
                            <flux:navlist.item icon="clipboard-document-list" :href="route('comandas.index')" :current="request()->routeIs('comandas.*')" wire:navigate>{{ __('Comandas') }}</flux:navlist.item>
                        @endif
                        @if (auth()->user()->hasPermission('customers.view'))
                            <flux:navlist.item icon="users" :href="route('admin.customers.index')" :current="request()->routeIs('admin.customers.index')" wire:navigate>{{ __('Clientes') }}</flux:navlist.item>
                        @endif
                        @if (auth()->user()->hasPermission('tables.view'))
                            <flux:navlist.item icon="calendar" :href="route('admin.mesas.index')" :current="request()->routeIs('admin.mesas.index')" wire:navigate>{{ __('Mesas') }}</flux:navlist.item>
                        @endif
                    </flux:navlist.group>
                @endif

                @if (auth()->user()->hasPermission('kds.view'))
                    <flux:navlist.group :heading="__('Producción')" class="grid">
                        <flux:navlist.item icon="beaker" :href="route('kds.index')" :current="request()->routeIs('kds.*')" wire:navigate>{{ __('Cocina / Barra') }}</flux:navlist.item>
                    </flux:navlist.group>
                @endif

                @if (auth()->user()->hasPermission('suppliers.manage') || auth()->user()->hasPermission('purchases.manage') || auth()->user()->hasPermission('menu.manage') || auth()->user()->hasPermission('orders.view') || auth()->user()->hasPermission('users.manage'))
                    <flux:navlist.group :heading="__('Gestión administrativa')" class="grid">
                        @if (auth()->user()->hasPermission('suppliers.manage'))
                            <flux:navlist.item icon="truck" :href="route('admin.proveedor.index')" :current="request()->routeIs('admin.proveedor.index')" wire:navigate>{{ __('Proveedores') }}</flux:navlist.item>
                        @endif
                        @if (auth()->user()->hasPermission('purchases.manage'))
                            <flux:navlist.item icon="shopping-bag" :href="route('admin.compra.index')" :current="request()->routeIs('admin.compra.index')" wire:navigate>{{ __('Compras') }}</flux:navlist.item>
                            <flux:navlist.item icon="tag" :href="route('admin.detalle.index')" :current="request()->routeIs('admin.detalle.index')" wire:navigate>{{ __('Detalles de Compra') }}</flux:navlist.item>
                        @endif
                        @if (auth()->user()->hasPermission('menu.manage'))
                            <flux:navlist.item icon="book-open" :href="route('admin.menu.index')" :current="request()->routeIs('admin.menu.*')" wire:navigate>{{ __('Carta') }}</flux:navlist.item>
                        @endif
                        @if (auth()->user()->hasPermission('orders.view'))
                            <flux:navlist.item icon="tag" :href="route('admin.orden.index')" :current="request()->routeIs('admin.orden.index')" wire:navigate>{{ __('Ordenes') }}</flux:navlist.item>
                        @endif
                        @if (auth()->user()->hasPermission('users.manage'))
                            <flux:navlist.item icon="users" :href="route('admin.users.index')" :current="request()->routeIs('admin.users.*')" wire:navigate>{{ __('Usuarios') }}</flux:navlist.item>
                        @endif
                    </flux:navlist.group>
                @endif
            </flux:navlist>

            <flux:spacer />

            <!-- Desktop User Menu -->
            <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon:trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
