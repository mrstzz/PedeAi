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

<x-layouts::app :title="__('Garçom')">
    <div class="flex min-h-[calc(100svh-7rem)] flex-col gap-3">
        <section class="rounded-lg border border-base-300 bg-base-100/95 p-3 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0 leading-tight">
                    <p class="text-xs font-medium uppercase text-base-content/50">Atendimento</p>
                    <h1 class="truncate text-xl font-bold text-neutral">Painel do garçom</h1>
                </div>
                <div class="min-w-0 rounded-md border border-primary/25 bg-primary/10 px-3 py-2 text-right">
                    <p class="truncate text-sm font-semibold text-primary">{{ auth()->user()->name }}</p>
                    <p class="text-[0.7rem] leading-tight text-base-content/55">turno ativo</p>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-2 gap-2">
            <a href="{{ route('ticket-list.create') }}" class="rounded-lg border border-primary/20 bg-primary p-4 text-primary-content shadow-sm transition hover:shadow-md active:scale-[0.99]" wire:navigate>
                <div class="flex min-h-20 flex-col justify-between">
                    <span class="flex size-8 items-center justify-center rounded-md bg-primary-content/15">
                        <flux:icon.plus class="size-4" />
                    </span>
                    <div>
                        <p class="text-base font-bold leading-tight">Nova comanda</p>
                        <p class="mt-1 text-xs leading-snug opacity-80">Mesa ou reserva</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('reservations.create') }}" class="rounded-lg border border-secondary/20 bg-secondary/10 p-4 text-secondary shadow-sm transition hover:bg-secondary/15 active:scale-[0.99]" wire:navigate>
                <div class="flex min-h-20 flex-col justify-between">
                    <span class="flex size-8 items-center justify-center rounded-md bg-secondary/15">
                        <flux:icon.calendar-days class="size-4" />
                    </span>
                    <div>
                        <p class="text-base font-bold leading-tight">Reserva</p>
                        <p class="mt-1 text-xs leading-snug text-base-content/65">Bloquear mesa</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('reservations.index') }}" class="col-span-2 rounded-lg border border-base-300 bg-base-100/95 px-4 py-3 shadow-sm" wire:navigate>
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-semibold leading-tight text-neutral">Ver reservas</p>
                        <p class="mt-0.5 truncate text-xs text-base-content/60">{{ $reservationsCount }} ativas no momento</p>
                    </div>
                    <flux:icon.chevron-right class="size-4 shrink-0 text-base-content/35" />
                </div>
            </a>
        </section>

        <section class="grid grid-cols-2 gap-2">
            <div class="rounded-lg border border-base-300 bg-base-100/95 px-4 py-3 shadow-sm">
                <div class="flex items-end justify-between gap-3">
                    <p class="text-xs leading-tight text-base-content/60">Comandas<br>ativas</p>
                    <p class="text-2xl font-bold leading-none text-primary">{{ $openTicketsCount }}</p>
                </div>
            </div>
            <div class="rounded-lg border border-base-300 bg-base-100/95 px-4 py-3 shadow-sm">
                <div class="flex items-end justify-between gap-3">
                    <p class="text-xs leading-tight text-base-content/60">Reservas<br>ativas</p>
                    <p class="text-2xl font-bold leading-none text-secondary">{{ $reservationsCount }}</p>
                </div>
            </div>
        </section>

        <section class="overflow-hidden rounded-lg border border-base-300 bg-base-100/95 shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b border-base-300 px-4 py-3">
                <h2 class="text-sm font-bold uppercase text-base-content/60">Comandas recentes</h2>
                <a href="{{ route('ticket-list.index') }}" class="link link-primary shrink-0 text-sm" wire:navigate>Ver todas</a>
            </div>

            @if ($recentTickets->isEmpty())
                <p class="px-4 py-5 text-sm text-base-content/60">Nenhuma comanda aberta ainda.</p>
            @else
                <div class="divide-y divide-base-300">
                    @foreach ($recentTickets as $ticket)
                        <a href="{{ route('ticket-list.show', $ticket) }}" class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-3 px-4 py-3 transition hover:bg-base-200/70" wire:navigate>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-neutral">Comanda #{{ $ticket->id }}</p>
                                <p class="truncate text-xs text-base-content/60">
                                    {{ $ticket->display_name }}{{ $ticket->table_number ? ' - Mesa '.$ticket->table_number : '' }}
                                </p>
                            </div>
                            <div class="min-w-20 text-right leading-tight">
                                <p class="text-sm font-bold">{{ $money($ticket->total_amount) }}</p>
                                <p class="text-[0.7rem] text-base-content/55">{{ $statusLabels[$ticket->status] ?? $ticket->status }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-layouts::app>
