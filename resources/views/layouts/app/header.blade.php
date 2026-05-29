<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body data-theme="pedeai" class="min-h-screen bg-base-200 text-base-content">
        <flux:header container class="border-b border-base-300 bg-base-100">
            <flux:sidebar.toggle class="lg:hidden mr-2" icon="bars-2" inset="left" />

            <x-app-logo href="{{ route('dashboard') }}" wire:navigate />

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Resumo') }}
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
                <flux:tooltip :content="__('Buscar')" position="bottom">
                    <flux:navbar.item class="!h-10 [&>div>svg]:size-5" icon="magnifying-glass" href="#" :label="__('Buscar')" />
                </flux:tooltip>
                <flux:tooltip :content="__('Comandas')" position="bottom">
                    <flux:navbar.item
                        class="h-10 max-lg:hidden [&>div>svg]:size-5"
                        icon="folder-git-2"
                        :href="route('ticket-list.index')"
                        :label="__('Comandas')"
                    />
                </flux:tooltip>
                @if (auth()->user()?->canAccessKitchenQueue())
                    <flux:tooltip :content="__('Fila de atendimento')" position="bottom">
                        <flux:navbar.item
                            class="h-10 max-lg:hidden [&>div>svg]:size-5"
                            icon="book-open-text"
                            :href="route('kitchen-queue.index')"
                            :label="__('Fila de atendimento')"
                        />
                    </flux:tooltip>
                @endif
                <flux:tooltip :content="__('Configuracoes')" position="bottom">
                    <flux:navbar.item
                        class="h-10 max-lg:hidden [&>div>svg]:size-5"
                        icon="book-open-text"
                        :href="route('profile.edit')"
                        :label="__('Configuracoes')"
                    />
                </flux:tooltip>
            </flux:navbar>

            <x-desktop-user-menu />
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar collapsible="mobile" sticky class="lg:hidden border-e border-base-300 bg-neutral text-neutral-content">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Atendimento')">
                    <flux:sidebar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Resumo')  }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="folder-git-2" :href="route('ticket-list.index')" wire:navigate>
                    {{ __('Comandas') }}
                </flux:sidebar.item>
                @if (auth()->user()?->canAccessKitchenQueue())
                    <flux:sidebar.item icon="book-open-text" :href="route('kitchen-queue.index')" wire:navigate>
                        {{ __('Fila de atendimento') }}
                    </flux:sidebar.item>
                @endif
                <flux:sidebar.item icon="book-open-text" :href="route('profile.edit')" wire:navigate>
                    {{ __('Configuracoes') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>
        </flux:sidebar>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
