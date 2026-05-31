@php
    $statusLabels = [
        'pendente' => 'Pendente',
        'confirmada' => 'Confirmada',
        'cancelada' => 'Cancelada',
        'concluida' => 'Concluida',
    ];

    $statusBadges = [
        'pendente' => 'badge-warning',
        'confirmada' => 'badge-primary',
        'cancelada' => 'badge-error',
        'concluida' => 'badge-success',
    ];
@endphp

<x-layouts::app :title="__('Reservas')">
    <div class="min-h-full bg-base-200 text-base-content">
        <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8">
            <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="badge badge-secondary badge-outline mb-3">Atendimento</div>
                    <h1 class="text-2xl font-bold text-neutral sm:text-3xl">Reservas</h1>
                    <p class="mt-2 max-w-2xl text-sm text-base-content/70">
                        Cadastre reservas, bloqueie mesas e abra comandas vinculadas quando o cliente chegar.
                    </p>
                </div>

                <x-link-button href="{{ route('reservations.create') }}" class="min-h-11">
                    Nova reserva
                </x-link-button>
            </section>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <x-card bodyClass="p-0">
                @if ($reservations->isEmpty())
                    <div class="flex flex-col items-center gap-4 p-10 text-center">
                        <div class="badge badge-outline badge-primary">Sem reservas</div>
                        <div>
                            <h2 class="text-lg font-semibold text-neutral">Nenhuma reserva cadastrada</h2>
                            <p class="mt-1 max-w-md text-sm text-base-content/65">
                                Ao cadastrar uma reserva, a mesa fica bloqueada para comandas normais.
                            </p>
                        </div>
                        <x-link-button href="{{ route('reservations.create') }}">Cadastrar reserva</x-link-button>
                    </div>
                @else
                    <div class="grid gap-3 p-4 md:hidden">
                        @foreach ($reservations as $reservation)
                            <div class="rounded-lg border border-base-300 bg-base-100 p-4 shadow-sm">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-neutral">{{ $reservation->customer_name }}</p>
                                        <p class="text-sm text-base-content/60">Mesa {{ $reservation->restaurantTable?->identifier ?? '-' }}</p>
                                    </div>
                                    <span class="badge shrink-0 {{ $statusBadges[$reservation->status] ?? 'badge-neutral' }}">
                                        {{ $statusLabels[$reservation->status] ?? $reservation->status }}
                                    </span>
                                </div>
                                <p class="mt-3 text-sm">{{ $reservation->reserved_at->timezone('America/Sao_Paulo')->format('d/m/Y H:i') }} - {{ $reservation->duration_minutes }} min</p>
                                @if (in_array($reservation->status, ['pendente', 'confirmada'], true))
                                    <div class="mt-4 grid grid-cols-2 gap-2">
                                        <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-ghost btn-sm" wire:navigate>Editar</a>
                                        <form method="POST" action="{{ route('reservations.cancel', $reservation) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-error btn-soft btn-sm w-full">Cancelar</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="hidden overflow-x-auto md:block">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Mesa</th>
                                    <th>Data e hora</th>
                                    <th>Status</th>
                                    <th>Comanda</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reservations as $reservation)
                                    <tr class="hover">
                                        <td>
                                            <div class="font-semibold text-neutral">{{ $reservation->customer_name }}</div>
                                            <div class="text-xs text-base-content/60">{{ $reservation->customer_phone ?: '-' }}</div>
                                        </td>
                                        <td>Mesa {{ $reservation->restaurantTable?->identifier ?? '-' }}</td>
                                        <td>
                                            <div>{{ $reservation->reserved_at->timezone('America/Sao_Paulo')->format('d/m/Y H:i') }}</div>
                                            <div class="text-xs text-base-content/60">{{ $reservation->duration_minutes }} min</div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $statusBadges[$reservation->status] ?? 'badge-neutral' }}">
                                                {{ $statusLabels[$reservation->status] ?? $reservation->status }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($reservation->ticket)
                                                <a href="{{ route('ticket-list.show', $reservation->ticket) }}" class="link link-primary" wire:navigate>
                                                    Comanda #{{ $reservation->ticket->id }}
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            @if (in_array($reservation->status, ['pendente', 'confirmada'], true))
                                                <div class="flex justify-end gap-2">
                                                    <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-ghost btn-sm" wire:navigate>Editar</a>
                                                    <form method="POST" action="{{ route('reservations.cancel', $reservation) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-error btn-soft btn-sm">
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
                        <div class="border-t border-base-300 p-4">
                            {{ $reservations->links() }}
                        </div>
                    @endif
                @endif
            </x-card>
        </div>
    </div>
</x-layouts::app>
