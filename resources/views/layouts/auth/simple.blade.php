<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body data-theme="pedeai" class="min-h-screen bg-base-100 text-base-content antialiased">
        <div class="h-2 w-full bg-primary"></div>

        <div class="flex min-h-[calc(100svh-2rem)] flex-col items-center justify-center bg-base-100 px-6 py-10">
            <div class="flex w-full max-w-md flex-col gap-8">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-3 font-medium" wire:navigate>
                    <span class="flex size-36 items-center justify-center">
                        <x-app-logo-icon class="size-32" />
                    </span>

                    <span class="text-center text-5xl font-bold leading-none">
                        <span class="text-primary">Pede</span><span class="text-secondary">Ai</span>
                    </span>
                    <span class="text-center text-sm font-semibold uppercase text-base-content/55">Gestao digital de pedidos</span>
                </a>

                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>

        <footer class="fixed inset-x-0 bottom-0 bg-neutral/70 px-4 py-1 text-center text-sm text-neutral-content">
            Gestao Facil LTDA - 2026 © Todos os direitos reservados.
        </footer>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
