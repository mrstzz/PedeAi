<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body data-theme="pedeai" class="app-texture-bg min-h-screen text-base-content">
        <div class="min-h-screen pb-20">
            <header class="sticky top-0 z-30 border-b border-base-300 bg-base-100/90 px-3 py-2 backdrop-blur">
                <div class="mx-auto flex max-w-4xl items-center justify-between gap-3">
                    <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-2" wire:navigate>
                        <span class="flex size-8 shrink-0 items-center justify-center overflow-hidden rounded-md bg-white">
                            <x-app-logo-icon class="size-7" />
                        </span>
                        <div class="min-w-0 leading-tight">
                            <p class="text-sm font-bold text-neutral">PedeAi</p>
                            <p class="text-xs text-base-content/60">Garcom</p>
                        </div>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                        @csrf
                        <button type="submit" class="btn btn-ghost btn-sm">Sair</button>
                    </form>
                </div>
            </header>

            <main class="mx-auto w-full max-w-4xl px-3 py-3 sm:px-5 sm:py-5">
                {{ $slot }}
            </main>
        </div>

        <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-base-300 bg-base-100/95 px-3 pb-2 pt-1.5 shadow-lg backdrop-blur">
            <div class="mx-auto grid max-w-4xl grid-cols-3 gap-2">
                <a
                    href="{{ route('ticket-list.create') }}"
                    class="btn min-h-11 flex-col gap-0 rounded-md border-0 text-xs {{ request()->routeIs('ticket-list.create') ? 'btn-primary' : 'btn-ghost text-base-content/70' }}"
                    wire:navigate
                >
                    <flux:icon.plus class="size-4" />
                    Comanda
                </a>

                <a
                    href="{{ route('reservations.index') }}"
                    class="btn min-h-11 flex-col gap-0 rounded-md border-0 text-xs {{ request()->routeIs('reservations.*') ? 'btn-secondary' : 'btn-ghost text-base-content/70' }}"
                    wire:navigate
                >
                    <flux:icon.calendar-days class="size-4" />
                    Reservas
                </a>

                <a
                    href="{{ route('dashboard') }}"
                    class="btn min-h-11 flex-col gap-0 rounded-md border-0 text-xs {{ request()->routeIs('dashboard') ? 'btn-neutral' : 'btn-ghost text-base-content/70' }}"
                    wire:navigate
                >
                    <flux:icon.home class="size-4" />
                    Inicio
                </a>
            </div>
        </nav>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
