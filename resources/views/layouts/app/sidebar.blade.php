<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body data-theme="pedeai" class="app-texture-bg min-h-screen text-base-content">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-base-300 bg-neutral text-neutral-content">
            <flux:sidebar.header class="border-b border-neutral-content/10 pb-4">
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <div class="px-3 py-3">
                <div class="rounded-lg border border-neutral-content/10 bg-neutral-content/10 p-3">
                    <p class="text-xs font-medium uppercase text-neutral-content/60">Operação</p>
                    <p class="mt-1 text-sm font-semibold text-neutral-content">Comandas e tickets</p>
                </div>
            </div>

            <flux:sidebar.nav class="px-2">
                <flux:sidebar.group :heading="__('Atendimento')" class="grid text-neutral-content/70">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" class="text-neutral-content hover:bg-neutral-content/10 data-current:bg-primary data-current:text-content" wire:navigate>
                        {{ __('Resumo') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="folder-git-2" :href="route('ticket-list.index')" :current="request()->routeIs('ticket-list.index')" class="text-neutral-content hover:bg-neutral-content/10 data-current:bg-primary data-current:text-content" wire:navigate>
                        {{ __('Comandas') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="layout-grid" :href="route('ticket-list.create')" :current="request()->routeIs('ticket-list.create')" class="text-neutral-content hover:bg-neutral-content/10 data-current:bg-primary data-current:text-content" wire:navigate>
                        {{ __('Nova comanda') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="book-open-text" :href="route('reservations.index')" :current="request()->routeIs('reservations.*')" class="text-neutral-content hover:bg-neutral-content/10 data-current:bg-primary data-current:text-content" wire:navigate>
                        {{ __('Reservas') }}
                    </flux:sidebar.item>

                    
                </flux:sidebar.group>

                @if (auth()->user()?->canAccessKitchenQueue())
                    <flux:sidebar.group :heading="__('Cozinha')" class="mt-6 grid text-neutral-content/70">
                        <flux:sidebar.item icon="book-open-text" :href="route('kitchen-queue.index')" :current="request()->routeIs('kitchen-queue.*')" class="text-neutral-content hover:bg-neutral-content/10 data-current:bg-primary data-current:text-content" wire:navigate>
                            {{ __('Fila de atendimento') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
            
                @endif

                @if (auth()->user()?->isAdmin())
                    <flux:sidebar.group :heading="__('Administração')" class="mt-6 grid text-neutral-content/70">
                        <flux:sidebar.item icon="layout-grid" :href="route('restaurant-tables.index')" :current="request()->routeIs('restaurant-tables.*')" class="text-neutral-content hover:bg-neutral-content/10 data-current:bg-primary data-current:text-content" wire:navigate>
                            {{ __('Mesas') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="book-open-text" :href="route('menu-items.index')" :current="request()->routeIs('menu-items.*')" class="text-neutral-content hover:bg-neutral-content/10 data-current:bg-primary data-current:text-content" wire:navigate>
                            {{ __('Itens da comanda') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="book-open-text" :href="route('users.index')" :current="request()->routeIs('users.*')" class="text-neutral-content hover:bg-neutral-content/10 data-current:bg-primary data-current:text-content" wire:navigate>
                            {{ __('Usuários') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endif
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav class="px-2">
                <flux:sidebar.item icon="book-open-text" :href="route('profile.edit')" :current="request()->routeIs('profile.*')" class="text-neutral-content/80 hover:bg-neutral-content/10 data-current:bg-primary data-current:text-content" wire:navigate>
                    {{ __('Configurações') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <div class="px-3 pb-2">
                <div class="rounded-lg border border-neutral-content/10 bg-neutral-content/10 px-3 py-2">
                    <p class="text-xs font-medium uppercase text-neutral-content/55">Permissão: </p>
                    <p class="mt-1 text-sm font-semibold text-neutral-content">{{ auth()->user()->role?->name ?? 'Sem permissão' }}</p>
                </div>
            </div>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
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
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                    <flux:text class="truncate">Role: {{ auth()->user()->role?->name ?? 'Sem role' }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Configurações') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Sair') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
