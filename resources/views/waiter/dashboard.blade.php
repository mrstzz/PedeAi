@php
    $money = fn ($value) => 'R$ '.number_format((float) $value, 2, ',', '.');
    $statusLabels = [
        'aberta' => 'Aberta',
        'em_andamento' => 'Em andamento',
        'fechada' => 'Fechada',
        'paga' => 'Paga',
        'cancelada' => 'Cancelada',
    ];
    $statusTones = [
        'aberta' => 'primary',
        'em_andamento' => 'info',
        'fechada' => 'warning',
        'paga' => 'success',
        'cancelada' => 'error',
    ];
@endphp

<x-layouts::app :title="__('Garçom')">
    <div class="flex min-h-[calc(100svh-7rem)] flex-col gap-4">
        <section class="rounded-lg border border-base-300/80 bg-base-100/95 p-4 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0 leading-tight">
                    <div class="mb-2 inline-flex items-center gap-2 rounded-md border border-secondary/25 bg-secondary/10 px-2.5 py-1 text-[0.68rem] font-semibold uppercase text-secondary">
                        <span class="size-1.5 rounded-full bg-secondary"></span>
                        Atendimento
                    </div>
                    <h1 class="truncate text-2xl font-bold text-neutral">Painel do garçom</h1>
                    <p class="mt-1 truncate text-sm text-base-content/60">{{ auth()->user()->name }}</p>
                </div>
                <div class="grid size-12 shrink-0 place-items-center rounded-lg bg-primary/10 text-primary ring-1 ring-primary/15">
                    <flux:icon.user class="size-6" />
                </div>
            </div>
        </section>

        <section class="grid grid-cols-2 gap-3">
            <a href="{{ route('ticket-list.create') }}" class="rounded-lg border border-primary/20 bg-primary p-4 text-primary-content shadow-sm transition hover:shadow-md active:scale-[0.99]" wire:navigate>
                <div class="flex min-h-28 flex-col justify-between">
                    <span class="flex size-10 items-center justify-center rounded-lg bg-primary-content/15">
                        <flux:icon.plus class="size-5" />
                    </span>
                    <div>
                        <p class="text-lg font-bold leading-tight">Nova comanda</p>
                        <p class="mt-1 text-xs leading-snug opacity-80">Mesa ou reserva</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('reservations.create') }}" class="rounded-lg border border-secondary/20 bg-secondary/10 p-4 text-secondary shadow-sm transition hover:bg-secondary/15 active:scale-[0.99]" wire:navigate>
                <div class="flex min-h-28 flex-col justify-between">
                    <span class="flex size-10 items-center justify-center rounded-lg bg-secondary/15">
                        <flux:icon.calendar-days class="size-5" />
                    </span>
                    <div>
                        <p class="text-lg font-bold leading-tight">Reserva</p>
                        <p class="mt-1 text-xs leading-snug text-base-content/65">Bloquear mesa</p>
                    </div>
                </div>
            </a>
        </section>

        <section class="grid grid-cols-2 gap-3">
            <x-metric-card
                label="Comandas ativas"
                :value="$openTicketsCount"
                description="Abertas no atendimento"
                icon="ticket"
                accent="text-primary bg-primary/10 ring-primary/15"
                class="p-4"
            />
            <x-metric-card
                label="Reservas ativas"
                :value="$reservationsCount"
                description="Pendentes ou confirmadas"
                icon="calendar-days"
                accent="text-secondary bg-secondary/10 ring-secondary/15"
                class="p-4"
            />
        </section>

        <a href="{{ route('reservations.index') }}" class="rounded-lg border border-base-300/80 bg-base-100/95 px-4 py-3 shadow-sm transition hover:bg-primary/5" wire:navigate>
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-semibold leading-tight text-neutral">Ver reservas</p>
                    <p class="mt-0.5 truncate text-xs text-base-content/60">{{ $reservationsCount }} ativas no momento</p>
                </div>
                <flux:icon.chevron-right class="size-4 shrink-0 text-base-content/35" />
            </div>
        </a>

        <section class="overflow-hidden rounded-lg border border-base-300/80 bg-base-100/95 shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b border-base-300/80 px-4 py-3">
                <div>
                    <h2 class="text-sm font-bold uppercase text-base-content/60">Comandas recentes</h2>
                    <p class="text-xs text-base-content/50">Toque para abrir detalhes</p>
                </div>
                <a href="{{ route('ticket-list.index') }}" class="btn btn-primary btn-soft btn-sm gap-1" wire:navigate>
                    Ver todas
                    <flux:icon.chevron-right class="size-3" />
                </a>
            </div>

            @if ($recentTickets->isEmpty())
                <x-empty-state
                    title="Nenhuma comanda aberta"
                    description="Quando você abrir uma comanda, ela aparecerá aqui para acompanhamento rápido."
                    icon="ticket"
                    class="p-8"
                />
            @else
                <div class="divide-y divide-base-300/70">
                    @foreach ($recentTickets as $ticket)
                        <a href="{{ route('ticket-list.show', $ticket) }}" class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-3 px-4 py-3 transition hover:bg-base-200/70 active:bg-primary/5" wire:navigate>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="truncate text-sm font-semibold text-neutral">Comanda #{{ $ticket->id }}</p>
                                    <x-status-pill :label="$statusLabels[$ticket->status] ?? $ticket->status" :tone="$statusTones[$ticket->status] ?? 'neutral'" class="px-2 py-0.5" />
                                </div>
                                <p class="mt-1 truncate text-xs text-base-content/60">
                                    {{ $ticket->display_name }}{{ $ticket->table_number ? ' - Mesa '.$ticket->table_number : '' }}
                                </p>
                            </div>
                            <div class="min-w-20 text-right leading-tight">
                                <p class="text-sm font-bold text-neutral">{{ $money($ticket->total_amount) }}</p>
                                <p class="text-[0.7rem] text-base-content/55">{{ optional($ticket->opened_at)->format('d/m H:i') ?? '-' }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-layouts::app>
