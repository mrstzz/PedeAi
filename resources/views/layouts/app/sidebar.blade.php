<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body data-theme="pedeai" class="app-texture-bg min-h-screen text-base-content"
    x-data="{
        sidebarOpen: null,
        isMobile: false,
        sidebar: null,
        checkSidebarState() {
            if (!this.sidebar) return;
            if (this.isMobile) {
                this.sidebarOpen = !this.sidebar.hasAttribute('data-flux-sidebar-collapsed-mobile');
            } else {
                this.sidebarOpen = !this.sidebar.hasAttribute('data-flux-sidebar-stashed');
            }
        }
    }"
    x-init="
        sidebar = document.querySelector('[data-flux-sidebar]');
        isMobile = window.innerWidth < 1024;
        checkSidebarState();

        if (sidebar) {
            new MutationObserver(() => checkSidebarState())
                .observe(sidebar, { attributes: true, attributeFilter: ['data-flux-sidebar-stashed', 'data-flux-sidebar-collapsed-mobile'] });
        }

        window.addEventListener('resize', () => {
            isMobile = window.innerWidth < 1024;
            checkSidebarState();
        });
    "
>
    <flux:sidebar
        sticky
        collapsible="mobile"
        stashable
        class="border-e border-[var(--color-sidebar-muted)] bg-[var(--color-sidebar-bg)] text-[var(--color-sidebar-content)] shadow-xl shadow-neutral/10"
    >
        <flux:sidebar.header class="border-b border-[var(--color-sidebar-muted)] px-3 pb-4 pt-3">
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
        </flux:sidebar.header>

        <div class="px-3 py-3">
            <div class="rounded-lg border border-[var(--color-sidebar-muted)] bg-[var(--color-sidebar-muted)] p-3 shadow-inner">
                <p class="text-xs font-semibold uppercase text-[var(--color-neutral)]">Operação</p>
                <p class="mt-1 text-sm font-semibold text-[var(--color-sidebar-content)]">Comandas e atendimento</p>
                <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-[var(--color-sidebar-muted)]">
                    <div class="h-full w-2/3 rounded-full bg-secondary"></div>
                </div>
            </div>
        </div>

        <flux:sidebar.nav class="px-2">
            <flux:sidebar.group :heading="__('Atendimento')" class="grid text-[var(--color-sidebar-muted)]">
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" class="rounded-md text-[var(--color-sidebar-content)] hover:bg-[var(--color-sidebar-muted)] hover:text-[var(--color-sidebar-content)] data-current:bg-primary data-current:text-primary-content data-current:shadow-sm" wire:navigate>
                    {{ __('Resumo') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="clipboard-document-list" :href="route('ticket-list.index')" :current="request()->routeIs('ticket-list.index')" class="rounded-md text-[var(--color-sidebar-content)] hover:bg-[var(--color-sidebar-muted)] hover:text-[var(--color-sidebar-content)] data-current:bg-primary data-current:text-primary-content data-current:shadow-sm" wire:navigate>
                    {{ __('Comandas') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="plus-circle" :href="route('ticket-list.create')" :current="request()->routeIs('ticket-list.create')" class="rounded-md text-[var(--color-sidebar-content)] hover:bg-[var(--color-sidebar-muted)] hover:text-[var(--color-sidebar-content)] data-current:bg-primary data-current:text-primary-content data-current:shadow-sm" wire:navigate>
                    {{ __('Nova comanda') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="calendar-days" :href="route('reservations.index')" :current="request()->routeIs('reservations.*')" class="rounded-md text-[var(--color-sidebar-content)] hover:bg-[var(--color-sidebar-muted)] hover:text-[var(--color-sidebar-content)] data-current:bg-primary data-current:text-primary-content data-current:shadow-sm" wire:navigate>
                    {{ __('Reservas') }}
                </flux:sidebar.item>
            </flux:sidebar.group>

            @if (auth()->user()?->canAccessKitchenQueue())
            <flux:sidebar.group :heading="__('Cozinha')" class="mt-6 grid text-[var(--color-sidebar-muted)]">
                <flux:sidebar.item icon="queue-list" :href="route('kitchen-queue.index')" :current="request()->routeIs('kitchen-queue.*')" class="rounded-md text-[var(--color-sidebar-content)] hover:bg-[var(--color-sidebar-muted)] hover:text-[var(--color-sidebar-content)] data-current:bg-primary data-current:text-primary-content data-current:shadow-sm" wire:navigate>
                    {{ __('Fila de atendimento') }}
                </flux:sidebar.item>
            </flux:sidebar.group>
            @endif

            @if (auth()->user()?->isAdmin())
            <flux:sidebar.group :heading="__('Administração')" class="mt-6 grid text-[var(--color-sidebar-muted)]">
                <flux:sidebar.item icon="table-cells" :href="route('restaurant-tables.index')" :current="request()->routeIs('restaurant-tables.*')" class="rounded-md text-[var(--color-sidebar-content)] hover:bg-[var(--color-sidebar-muted)] hover:text-[var(--color-sidebar-content)] data-current:bg-primary data-current:text-primary-content data-current:shadow-sm" wire:navigate>
                    {{ __('Mesas') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="book-open" :href="route('menu-items.index')" :current="request()->routeIs('menu-items.*')" class="rounded-md text-[var(--color-sidebar-content)] hover:bg-[var(--color-sidebar-muted)] hover:text-[var(--color-sidebar-content)] data-current:bg-primary data-current:text-primary-content data-current:shadow-sm" wire:navigate>
                    {{ __('Itens da comanda') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="users" :href="route('users.index')" :current="request()->routeIs('users.*')" class="rounded-md text-[var(--color-sidebar-content)] hover:bg-[var(--color-sidebar-muted)] hover:text-[var(--color-sidebar-content)] data-current:bg-primary data-current:text-primary-content data-current:shadow-sm" wire:navigate>
                    {{ __('Usuários') }}
                </flux:sidebar.item>
            </flux:sidebar.group>
            @endif
        </flux:sidebar.nav>

        <flux:spacer />

        <flux:sidebar.nav class="px-2">
            <flux:sidebar.item icon="cog-6-tooth" :href="route('profile.edit')" :current="request()->routeIs('profile.*')" class="rounded-md text-[var(--color-sidebar-content)] hover:bg-[var(--color-sidebar-muted)] hover:text-[var(--color-sidebar-content)] data-current:bg-primary data-current:text-primary-content data-current:shadow-sm" wire:navigate>
                {{ __('Configurações') }}
            </flux:sidebar.item>
        </flux:sidebar.nav>

        <div class="px-3 pb-2">
            <div class="rounded-lg border border-[var(--color-sidebar-muted)] bg-[var(--color-sidebar-muted)] px-3 py-2">
                <p class="text-xs font-semibold uppercase text-[var(--color-neutral)]">Permissão</p>
                <p class="mt-1 text-sm font-semibold text-[var(--color-sidebar-content)]">{{ auth()->user()->role?->name ?? 'Sem permissão' }}</p>
            </div>
        </div>

        <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
    </flux:sidebar>

    <div class="hidden" aria-hidden="true">
        <flux:sidebar.toggle id="flux-sidebar-toggle-proxy" />
    </div>

    <button
        type="button"
        @click="
            if (isMobile) {
                document.getElementById('flux-sidebar-toggle-proxy').click();
            } else {
                if (sidebar && sidebar.hasAttribute('data-flux-sidebar-stashed')) {
                    sidebar.removeAttribute('data-flux-sidebar-stashed');
                } else if (sidebar) {
                    sidebar.setAttribute('data-flux-sidebar-stashed', '');
                }
            }
        "
        class="fixed top-6 z-50 transition-all duration-300
               bg-[var(--color-sidebar-bg)] text-[var(--color-sidebar-content)]
               border border-[var(--color-sidebar-muted)]
               hover:bg-[var(--color-sidebar-muted)]
               size-10 inline-flex items-center justify-center"
        :class="sidebarOpen
            ? 'left-[calc(16rem-1px)] border-l-0 rounded-r-lg shadow-sm'
            : 'left-4 rounded-lg shadow-md'"
        aria-label="Toggle sidebar"
    >
        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    {{ $slot }}

    @persist('toast')
    <flux:toast.group>
        <flux:toast />
    </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>