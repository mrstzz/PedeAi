@php
    $itemLabels = [
        'pendente' => 'Pendente',
        'em_preparo' => 'Em preparo',
        'entregue' => 'Entregue',
    ];
    $itemBadges = [
        'pendente' => 'badge-warning',
        'em_preparo' => 'badge-info',
        'entregue' => 'badge-success',
    ];
@endphp

<x-layouts::app :title="__('Fila de atendimento')">
    <div class="min-h-full bg-base-200 text-base-content">
        <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8">
            <section>
                <div class="badge badge-secondary badge-outline mb-3">Cozinha</div>
                <h1 class="text-2xl font-bold text-neutral sm:text-3xl">Fila de atendimento</h1>
                <p class="mt-2 max-w-2xl text-sm text-base-content/70">
                    Comandas abertas ordenadas por prioridade e chegada.
                </p>
            </section>

            @if ($tickets->isEmpty())
                <x-card bodyClass="items-center p-10 text-center">
                    <div class="badge badge-outline badge-primary">Fila vazia</div>
                    <h2 class="text-lg font-semibold text-neutral">Nenhuma comanda aguardando preparo</h2>
                </x-card>
            @else
                <div class="grid gap-4 xl:grid-cols-2">
                    @foreach ($tickets as $ticket)
                        <x-card class="{{ $ticket->priority === 'alta' ? 'border-error/40' : '' }}">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 class="text-lg font-bold text-neutral">Comanda #{{ $ticket->id }}</h2>
                                        <span class="badge {{ $ticket->priority === 'alta' ? 'badge-error' : 'badge-ghost' }}">
                                            {{ $ticket->priority === 'alta' ? 'Alta' : 'Normal' }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-base-content/65">
                                        {{ $ticket->display_name }}{{ $ticket->table_number ? ' - Mesa '.$ticket->table_number : '' }}
                                    </p>
                                    <p class="text-xs text-base-content/55">
                                        Chegou em {{ optional($ticket->opened_at)->format('d/m/Y H:i') ?? '-' }}
                                    </p>
                                </div>

                                @if ($ticket->status === 'aberta')
                                    <x-form :action="route('ticket-list.start-preparation', $ticket)" post>
                                        <x-primary-button type="submit" class="btn-sm">Pegar comanda</x-primary-button>
                                    </x-form>
                                @else
                                    <span class="badge badge-info">Em andamento</span>
                                @endif
                            </div>

                            <div class="divide-y divide-base-300">
                                @foreach ($ticket->items as $item)
                                    <div class="flex flex-col gap-3 py-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="font-semibold text-neutral">{{ $item->quantity }}x {{ $item->product_name }}</p>
                                            @if ($item->notes)
                                                <p class="text-xs text-base-content/60">{{ $item->notes }}</p>
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <span class="badge {{ $itemBadges[$item->status] ?? 'badge-neutral' }}">
                                                {{ $itemLabels[$item->status] ?? $item->status }}
                                            </span>

                                            @if ($item->status !== 'entregue')
                                                <x-form :action="route('ticket-items.deliver', $item)" post>
                                                    <x-secondary-button type="submit" class="btn-sm">Entregar</x-secondary-button>
                                                </x-form>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </x-card>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts::app>
