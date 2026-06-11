@php
    $money = fn ($value) => 'R$ '.number_format((float) $value, 2, ',', '.');
    $statusLabels = [
        'aberta' => 'Aberta',
        'em_andamento' => 'Em andamento',
        'fechada' => 'Fechada',
        'paga' => 'Paga',
        'cancelada' => 'Cancelada',
    ];
    $statusBadges = [
        'aberta' => 'badge-primary',
        'em_andamento' => 'badge-info',
        'fechada' => 'badge-warning',
        'paga' => 'badge-success',
        'cancelada' => 'badge-error',
    ];
@endphp

<x-layouts::app :title="__('Comandas')">
    <div class="min-h-full text-base-content">
        <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8">
            <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="badge badge-primary badge-outline mb-3">Tickets</div>
                    <h1 class="text-2xl font-bold text-neutral sm:text-3xl">Comandas</h1>
                    <p class="mt-2 max-w-2xl text-sm text-base-content/70">
                        Consulte as comandas cadastradas, seus status, valores e quantidade de itens.
                    </p>
                </div>

            </section>

            <x-card bodyClass="p-0">
                @if ($tickets->isEmpty())
                    <div class="flex flex-col items-center gap-4 p-10 text-center">
                        <div class="badge badge-outline badge-primary">Sem registros</div>
                        <div>
                            <h2 class="text-lg font-semibold text-neutral">Nenhuma comanda cadastrada</h2>
                            <p class="mt-1 max-w-md text-sm text-base-content/65">
                                Cadastre a primeira comanda para acompanhar atendimento, status e valores.
                            </p>
                        </div>
                        <x-link-button href="{{ route('ticket-list.create') }}">Cadastrar comanda</x-link-button>
                    </div>
                @else
                    <div class="grid gap-3 p-4 md:hidden">
                        @foreach ($tickets as $ticket)
                            <a href="{{ route('ticket-list.show', $ticket) }}" class="rounded-lg border border-base-300 bg-base-100 p-4 shadow-sm" wire:navigate>
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-neutral">Comanda #{{ $ticket->id }}</p>
                                        <p class="truncate text-sm text-base-content/60">{{ $ticket->display_name }}{{ $ticket->table_number ? ' - Mesa '.$ticket->table_number : '' }}</p>
                                    </div>
                                    <span class="badge shrink-0 {{ $statusBadges[$ticket->status] ?? 'badge-neutral' }}">
                                        {{ $statusLabels[$ticket->status] ?? $ticket->status }}
                                    </span>
                                </div>
                                <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                                    <div><span class="text-base-content/55">Itens</span><br><strong>{{ $ticket->items_count }}</strong></div>
                                    <div class="text-right"><span class="text-base-content/55">Total</span><br><strong>{{ $money($ticket->total_amount) }}</strong></div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="hidden overflow-x-auto md:block">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Comanda</th>
                                    <th>Cliente</th>
                                    <th>Mesa</th>
                                    <th>Status</th>
                                    <th>Prioridade</th>
                                    <th>Itens</th>
                                    <th>Total</th>
                                    <th>Abertura</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tickets as $ticket)
                                    <tr class="hover">
                                        <td class="font-semibold text-neutral">#{{ $ticket->id }}</td>
                                        <td>{{ $ticket->display_name }}</td>
                                        <td>{{ $ticket->table_number ?: '-' }}</td>
                                        <td>
                                            <span class="badge {{ $statusBadges[$ticket->status] ?? 'badge-neutral' }}">
                                                {{ $statusLabels[$ticket->status] ?? $ticket->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $ticket->priority === 'alta' ? 'badge-error' : 'badge-ghost' }}">
                                                {{ $ticket->priority === 'alta' ? 'Alta' : 'Normal' }}
                                            </span>
                                        </td>
                                        <td>{{ $ticket->items_count }}</td>
                                        <td>{{ $money($ticket->total_amount) }}</td>
                                        <td>{{ optional($ticket->opened_at)->format('d/m/Y H:i') ?? '-' }}</td>
                                        <td class="text-right">
                                            <a href="{{ route('ticket-list.show', $ticket) }}" class="btn btn-ghost btn-sm">Detalhes</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($tickets->hasPages())
                        <div class="border-t border-base-300 p-4">
                            {{ $tickets->links() }}
                        </div>
                    @endif
                @endif
            </x-card>
        </div>
    </div>
</x-layouts::app>
