@php
    $statusLabels = [
        'pendente' => 'Pendente',
        'confirmada' => 'Confirmada',
        'cancelada' => 'Cancelada',
        'concluida' => 'Concluída',
    ];

    $statusMeta = [
        'pendente' => [
            'badge' => 'bg-warning/15 text-warning ring-warning/30',
            'dot' => 'bg-warning',
            'icon' => 'clock',
        ],
        'confirmada' => [
            'badge' => 'bg-primary/10 text-primary ring-primary/20',
            'dot' => 'bg-primary',
            'icon' => 'calendar-days',
        ],
        'cancelada' => [
            'badge' => 'bg-error/10 text-error ring-error/20',
            'dot' => 'bg-error',
            'icon' => 'x-circle',
        ],
        'concluida' => [
            'badge' => 'bg-success/10 text-success ring-success/20',
            'dot' => 'bg-success',
            'icon' => 'check-circle',
        ],
    ];

    $reservationItems = collect($reservations->items());
    $activeReservations = $reservationItems->whereIn('status', ['pendente', 'confirmada'])->count();
    $reservationMetrics = [
        [
            'label' => 'Reservas nesta página',
            'value' => $reservationItems->count(),
            'description' => 'Registros recentes',
            'icon' => 'calendar-days',
            'accent' => 'text-primary bg-primary/10 ring-primary/15',
        ],
        [
            'label' => 'Ativas',
            'value' => $activeReservations,
            'description' => 'Pendentes ou confirmadas',
            'icon' => 'clock',
            'accent' => 'text-info bg-info/10 ring-info/15',
        ],
        [
            'label' => 'Concluídas',
            'value' => $reservationItems->where('status', 'concluida')->count(),
            'description' => 'Atendimento finalizado',
            'icon' => 'check-circle',
            'accent' => 'text-success bg-success/10 ring-success/15',
        ],
        [
            'label' => 'Com comanda',
            'value' => $reservationItems->filter(fn ($reservation) => filled($reservation->ticket))->count(),
            'description' => 'Vinculadas a comandas',
            'icon' => 'ticket',
            'accent' => 'text-secondary bg-secondary/10 ring-secondary/15',
        ],
    ];
@endphp

<x-layouts::app :title="__('Reservas')">
    <div class="min-h-full text-base-content">
        <div class="mx-auto flex w-full max-w-[100rem] flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8 2xl:px-10">
            <section class="rounded-lg border border-base-300/80 bg-base-100/90 p-5 shadow-sm backdrop-blur sm:p-6">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div class="min-w-0">
                        <div class="mb-3 inline-flex items-center gap-2 rounded-md border border-secondary/25 bg-secondary/10 px-3 py-1 text-xs font-semibold uppercase tracking-normal text-secondary">
                            <span class="size-1.5 rounded-full bg-secondary"></span>
                            Atendimento
                        </div>
                        <h1 class="text-3xl font-bold tracking-normal text-neutral sm:text-4xl">Reservas</h1>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-base-content/65">
                            Cadastre reservas, bloqueie mesas e abra comandas vinculadas quando o cliente chegar.
                        </p>
                    </div>

                    <x-link-button href="{{ route('reservations.create') }}" class="min-h-11 gap-2 whitespace-nowrap px-4">
                        <flux:icon.plus class="size-4" />
                        Nova reserva
                    </x-link-button>
                </div>
            </section>

            @if (session('status'))
                <div class="alert alert-success rounded-lg border border-success/20 bg-success/10 text-success">
                    <flux:icon.check-circle class="size-5" />
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error rounded-lg border border-error/20 bg-error/10 text-error">
                    <flux:icon.exclamation-triangle class="size-5" />
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($reservationMetrics as $metric)
                    <article class="rounded-lg border border-base-300/80 bg-base-100 p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-base-content/60">{{ $metric['label'] }}</p>
                                <p class="mt-2 text-4xl font-bold tracking-normal text-neutral">{{ $metric['value'] }}</p>
                            </div>

                            <x-icon-mark :icon="$metric['icon']" :accent="$metric['accent']" class="size-7" />
                        </div>

                        <p class="mt-4 border-t border-base-300/70 pt-3 text-sm text-base-content/60">{{ $metric['description'] }}</p>
                    </article>
                @endforeach
            </section>

            <x-card bodyClass="p-0">
                <div class="flex flex-col gap-3 border-b border-base-300/80 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                    <div>
                        <h2 class="text-lg font-semibold text-neutral">Agenda de reservas</h2>
                        <p class="text-sm text-base-content/55">Clientes, mesas, horários e comandas vinculadas.</p>
                    </div>

                    <div class="inline-flex items-center gap-2 rounded-md border border-base-300 bg-base-200/70 px-3 py-2 text-sm text-base-content/65">
                        <flux:icon.calendar-days class="size-4 text-primary" />
                        {{ $reservationItems->count() }} registro{{ $reservationItems->count() === 1 ? '' : 's' }}
                    </div>
                </div>

                @if ($reservations->isEmpty())
                    <div class="flex flex-col items-center gap-4 p-10 text-center">
                        <flux:icon.calendar-days class="size-8 text-primary" />
                        <div>
                            <h2 class="text-lg font-semibold text-neutral">Nenhuma reserva cadastrada</h2>
                            <p class="mt-1 max-w-md text-sm text-base-content/65">
                                Ao cadastrar uma reserva, a mesa fica bloqueada para comandas normais.
                            </p>
                        </div>
                        <x-link-button href="{{ route('reservations.create') }}" class="gap-2">
                            <flux:icon.plus class="size-4" />
                            Cadastrar reserva
                        </x-link-button>
                    </div>
                @else
                    <div class="grid gap-3 p-4 md:hidden">
                        @foreach ($reservations as $reservation)
                            <div class="rounded-lg border border-base-300/80 bg-base-100 p-4 shadow-sm">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-neutral">{{ $reservation->customer_name }}</p>
                                        <p class="text-sm text-base-content/60">Mesa {{ $reservation->restaurantTable?->identifier ?? '-' }}</p>
                                        <p class="text-xs text-base-content/50">{{ $reservation->customer_phone ?: 'Sem telefone' }}</p>
                                    </div>
                                    <span class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusMeta[$reservation->status]['badge'] ?? 'bg-base-200 text-base-content ring-base-300' }}">
                                        <span class="size-1.5 rounded-full {{ $statusMeta[$reservation->status]['dot'] ?? 'bg-base-content/40' }}"></span>
                                        {{ $statusLabels[$reservation->status] ?? $reservation->status }}
                                    </span>
                                </div>
                                <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                                    <div class="rounded-md bg-base-200/70 p-3">
                                        <span class="text-xs text-base-content/50">Horário</span>
                                        <strong class="mt-1 block text-neutral">{{ $reservation->reserved_at->timezone('America/Sao_Paulo')->format('d/m H:i') }}</strong>
                                    </div>
                                    <div class="rounded-md bg-base-200/70 p-3">
                                        <span class="text-xs text-base-content/50">Duração</span>
                                        <strong class="mt-1 block text-neutral">{{ $reservation->duration_minutes }} min</strong>
                                    </div>
                                </div>
                                @if (in_array($reservation->status, ['pendente', 'confirmada'], true))
                                    <div class="mt-4 grid grid-cols-2 gap-2">
                                        <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-primary btn-soft btn-sm gap-2" wire:navigate>
                                            <flux:icon.pencil-square class="size-4" />
                                            Editar
                                        </a>
                                        <form method="POST" action="{{ route('reservations.cancel', $reservation) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-error btn-soft btn-sm w-full gap-2">
                                                <flux:icon.x-mark class="size-4" />
                                                Cancelar
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="hidden overflow-x-auto md:block">
                        <table class="min-w-full text-sm">
                            <thead class="bg-base-200/70 text-left text-xs font-semibold uppercase tracking-normal text-base-content/55">
                                <tr>
                                    <th class="px-5 py-4">Cliente</th>
                                    <th class="px-5 py-4">Mesa</th>
                                    <th class="px-5 py-4">Data e hora</th>
                                    <th class="px-5 py-4">Status</th>
                                    <th class="px-5 py-4">Comanda</th>
                                    <th class="px-5 py-4 text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-base-300/70">
                                @foreach ($reservations as $reservation)
                                    <tr class="transition hover:bg-primary/5">
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-3">
                                                <flux:icon.user class="size-6 text-primary" />
                                                <div>
                                                    <div class="font-semibold text-neutral">{{ $reservation->customer_name }}</div>
                                                    <div class="text-xs text-base-content/60">{{ $reservation->customer_phone ?: '-' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 font-semibold text-neutral">Mesa {{ $reservation->restaurantTable?->identifier ?? '-' }}</td>
                                        <td class="px-5 py-4">
                                            <div class="font-semibold text-neutral">{{ $reservation->reserved_at->timezone('America/Sao_Paulo')->format('d/m/Y H:i') }}</div>
                                            <div class="text-xs text-base-content/60">{{ $reservation->duration_minutes }} min</div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusMeta[$reservation->status]['badge'] ?? 'bg-base-200 text-base-content ring-base-300' }}">
                                                <span class="size-1.5 rounded-full {{ $statusMeta[$reservation->status]['dot'] ?? 'bg-base-content/40' }}"></span>
                                                {{ $statusLabels[$reservation->status] ?? $reservation->status }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4">
                                            @if ($reservation->ticket)
                                                <a href="{{ route('ticket-list.show', $reservation->ticket) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-primary hover:text-primary/80" wire:navigate>
                                                    Comanda #{{ $reservation->ticket->id }}
                                                    <flux:icon.chevron-right class="size-3" />
                                                </a>
                                            @else
                                                <span class="text-base-content/45">-</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            @if (in_array($reservation->status, ['pendente', 'confirmada'], true))
                                                <div class="flex justify-end gap-2">
                                                    <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-primary btn-soft btn-sm gap-2" wire:navigate>
                                                        <flux:icon.pencil-square class="size-4" />
                                                        Editar
                                                    </a>
                                                    <form method="POST" action="{{ route('reservations.cancel', $reservation) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-error btn-soft btn-sm gap-2">
                                                            <flux:icon.x-mark class="size-4" />
                                                            Cancelar
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($reservations->hasPages())
                        <div class="border-t border-base-300/80 p-4">
                            {{ $reservations->links() }}
                        </div>
                    @endif
                @endif
            </x-card>
        </div>
    </div>
</x-layouts::app>
