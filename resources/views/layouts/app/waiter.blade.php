<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body data-theme="pedeai" class="app-texture-bg min-h-screen text-base-content">
        <div class="min-h-screen pb-24">
            <header class="sticky top-0 z-30 border-b border-base-300/80 bg-base-100/90 px-3 py-2 backdrop-blur">
                <div class="mx-auto flex max-w-4xl items-center justify-between gap-3">
                    <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-2" wire:navigate>
                        <span class="flex size-9 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-base-300">
                            <x-app-logo-icon class="size-8" />
                        </span>
                        <div class="min-w-0 leading-tight">
                            <p class="text-sm font-bold text-neutral">PedeAi</p>
                            <p class="text-xs text-base-content/60">Garçom</p>
                        </div>
                    </a>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('ticket-list.index') }}" class="btn btn-ghost btn-sm px-2" aria-label="Comandas" wire:navigate>
                            <flux:icon.clipboard-document-list class="size-4" />
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                            @csrf
                            <button type="submit" class="btn btn-ghost btn-sm" data-loading-label="Saindo">Sair</button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="mx-auto w-full max-w-4xl px-3 py-3 sm:px-5 sm:py-5">
                {{ $slot }}
            </main>
        </div>

        <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-base-300/80 bg-base-100/95 px-3 pb-[max(0.5rem,env(safe-area-inset-bottom))] pt-2 shadow-lg backdrop-blur">
            <div class="mx-auto grid max-w-4xl grid-cols-3 gap-2">
                <a
                    href="{{ route('ticket-list.create') }}"
                    class="btn min-h-14 flex-col gap-1 rounded-lg border-0 text-xs {{ request()->routeIs('ticket-list.create') ? 'btn-primary' : 'btn-ghost text-base-content/70' }}"
                    wire:navigate
                >
                    <flux:icon.plus class="size-5" />
                    Comanda
                </a>

                <a
                    href="{{ route('reservations.index') }}"
                    class="btn min-h-14 flex-col gap-1 rounded-lg border-0 text-xs {{ request()->routeIs('reservations.*') ? 'btn-secondary' : 'btn-ghost text-base-content/70' }}"
                    wire:navigate
                >
                    <flux:icon.calendar-days class="size-5" />
                    Reservas
                </a>

                <a
                    href="{{ route('dashboard') }}"
                    class="btn min-h-14 flex-col gap-1 rounded-lg border-0 text-xs {{ request()->routeIs('dashboard') ? 'btn-neutral' : 'btn-ghost text-base-content/70' }}"
                    wire:navigate
                >
                    <flux:icon.home class="size-5" />
                    Início
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
