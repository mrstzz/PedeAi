<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body data-theme="pedeai" class="min-h-screen bg-base-200 text-base-content">
        <div class="min-h-screen pb-24">
            <header class="sticky top-0 z-30 border-b border-base-300 bg-base-100/95 px-4 py-2 backdrop-blur">
                <div class="mx-auto flex max-w-3xl items-center justify-between gap-3">
                    <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-2" wire:navigate>
                        <span class="flex size-9 shrink-0 items-center justify-center overflow-hidden rounded-md bg-white">
                            <x-app-logo-icon class="size-8" />
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

            <main class="mx-auto w-full max-w-3xl px-3 py-3 sm:px-4 sm:py-4">
                {{ $slot }}
            </main>
        </div>

        <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-base-300 bg-base-100 px-3 py-2 shadow-lg">
            <div class="mx-auto grid max-w-3xl grid-cols-3 gap-2">
                <a
                    href="{{ route('ticket-list.create') }}"
                    class="btn btn-primary btn-soft min-h-14 flex-col gap-0 text-xs {{ request()->routeIs('ticket-list.create') ? 'btn-active' : '' }}"
                    wire:navigate
                >
                    <span class="text-sm font-bold">+</span>
                    Comanda
                </a>

                <a
                    href="{{ route('reservations.index') }}"
                    class="btn btn-secondary btn-soft min-h-14 flex-col gap-0 text-xs {{ request()->routeIs('reservations.*') ? 'btn-active' : '' }}"
                    wire:navigate
                >
                    <span class="text-sm font-bold">R</span>
                    Reservas
                </a>

                <a
                    href="{{ route('dashboard') }}"
                    class="btn btn-ghost min-h-14 flex-col gap-0 text-xs {{ request()->routeIs('dashboard') ? 'btn-active' : '' }}"
                    wire:navigate
                >
                    <span class="text-sm font-bold">#</span>
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
