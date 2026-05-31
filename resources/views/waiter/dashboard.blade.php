@php
    $money = fn ($value) => 'R$ '.number_format((float) $value, 2, ',', '.');
    $statusLabels = [
        'aberta' => 'Aberta',
        'em_andamento' => 'Em andamento',
        'fechada' => 'Fechada',
        'paga' => 'Paga',
        'cancelada' => 'Cancelada',
    ];
@endphp

<x-layouts::app :title="__('Garcom')">
    <div class="flex min-h-[calc(100svh-7rem)] flex-col gap-3 sm:gap-4">
        <section class="rounded-lg border border-base-300 bg-base-100 p-4 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                    <p class="text-sm text-base-content/60">Atendimento</p>
                    <h1 class="text-2xl font-bold leading-tight text-neutral">Painel do garcom</h1>
                </div>
                <div class="badge badge-primary badge-outline h-auto max-w-full justify-start whitespace-normal px-3 py-2 text-left leading-tight">
                    {{ auth()->user()->name }}
                </div>
            </div>
        </section>

        <section class="grid gap-3">
            <a href="{{ route('ticket-list.create') }}" class="btn btn-primary h-auto min-h-22 justify-start rounded-lg px-5 py-5 text-left" wire:navigate>
                <div class="min-w-0">
                    <div class="text-lg font-bold leading-tight">Nova comanda</div>
                    <div class="mt-1 whitespace-normal text-sm font-normal leading-snug opacity-80">Abrir mesa livre ou reserva confirmada</div>
                </div>
            </a>

            <a href="{{ route('reservations.create') }}" class="btn btn-secondary btn-soft h-auto min-h-16 justify-start rounded-lg px-5 py-4 text-left" wire:navigate>
                <div class="min-w-0">
                    <div class="text-base font-bold leading-tight">Criar reserva</div>
                    <div class="mt-1 whitespace-normal text-sm font-normal leading-snug opacity-75">Cadastrar cliente e bloquear mesa</div>
                </div>
            </a>

            <a href="{{ route('reservations.index') }}" class="btn btn-ghost h-auto min-h-14 justify-start rounded-lg border border-base-300 bg-base-100 px-5 py-3 text-left" wire:navigate>
                <div class="min-w-0">
                    <div class="text-base font-bold leading-tight">Ver reservas</div>
                    <div class="mt-1 whitespace-normal text-sm font-normal leading-snug opacity-70">{{ $reservationsCount }} reservas ativas</div>
                </div>
            </a>
        </section>

        <section class="grid gap-3 sm:grid-cols-2">
            <div class="rounded-lg border border-base-300 bg-base-100 p-4">
                <p class="text-xs text-base-content/60">Comandas ativas</p>
                <p class="mt-1 text-2xl font-bold text-primary sm:text-3xl">{{ $openTicketsCount }}</p>
            </div>
            <div class="rounded-lg border border-base-300 bg-base-100 p-4">
                <p class="text-xs text-base-content/60">Reservas</p>
                <p class="mt-1 text-2xl font-bold text-secondary sm:text-3xl">{{ $reservationsCount }}</p>
            </div>
        </section>

        <section class="rounded-lg border border-base-300 bg-base-100 p-4 shadow-sm">
            <div class="mb-3 flex items-center justify-between gap-3">
                <h2 class="font-semibold text-neutral">Comandas recentes</h2>
                <a href="{{ route('ticket-list.index') }}" class="link link-primary shrink-0 text-sm" wire:navigate>Ver todas</a>
            </div>

            @if ($recentTickets->isEmpty())
                <p class="text-sm text-base-content/60">Nenhuma comanda aberta ainda.</p>
            @else
                <div class="divide-y divide-base-300">
                    @foreach ($recentTickets as $ticket)
                        <a href="{{ route('ticket-list.show', $ticket) }}" class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-3 py-3" wire:navigate>
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-neutral">Comanda #{{ $ticket->id }}</p>
                                <p class="truncate text-sm text-base-content/60">
                                    {{ $ticket->display_name }}{{ $ticket->table_number ? ' - Mesa '.$ticket->table_number : '' }}
                                </p>
                            </div>
                            <div class="min-w-20 text-right">
                                <p class="font-semibold leading-tight">{{ $money($ticket->total_amount) }}</p>
                                <p class="text-xs text-base-content/55">{{ $statusLabels[$ticket->status] ?? $ticket->status }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-layouts::app>
