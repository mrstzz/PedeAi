@php
    $itemLabels = [
        'pendente' => 'Pendente',
        'em_preparo' => 'Em preparo',
        'entregue' => 'Entregue',
    ];
    $itemMeta = [
        'pendente' => [
            'badge' => 'bg-warning/15 text-warning ring-warning/30',
            'dot' => 'bg-warning',
        ],
        'em_preparo' => [
            'badge' => 'bg-info/10 text-info ring-info/20',
            'dot' => 'bg-info',
        ],
        'entregue' => [
            'badge' => 'bg-success/10 text-success ring-success/20',
            'dot' => 'bg-success',
        ],
    ];

    $queueItems = $tickets->flatMap(fn ($ticket) => $ticket->items);
    $queueMetrics = [
        [
            'label' => 'Comandas na fila',
            'value' => $tickets->count(),
            'description' => 'Abertas ou em preparo',
            'icon' => 'queue-list',
            'accent' => 'text-primary bg-primary/10 ring-primary/15',
        ],
        [
            'label' => 'Prioridade alta',
            'value' => $tickets->where('priority', 'alta')->count(),
            'description' => 'Demandam atencao imediata',
            'icon' => 'exclamation-triangle',
            'accent' => 'text-error bg-error/10 ring-error/15',
        ],
        [
            'label' => 'Itens pendentes',
            'value' => $queueItems->where('status', 'pendente')->count(),
            'description' => 'Ainda não iniciados',
            'icon' => 'clock',
            'accent' => 'text-warning bg-warning/10 ring-warning/20',
        ],
        [
            'label' => 'Entregues',
            'value' => $queueItems->where('status', 'entregue')->count(),
            'description' => 'Concluidos nesta fila',
            'icon' => 'check-circle',
            'accent' => 'text-success bg-success/10 ring-success/15',
        ],
    ];
@endphp

<x-layouts::app :title="__('Fila de atendimento')">
    <div class="min-h-full text-base-content">
        <div class="mx-auto flex w-full max-w-[100rem] flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8 2xl:px-10">
            <section class="rounded-lg border border-base-300/80 bg-base-100/90 p-5 shadow-sm backdrop-blur sm:p-6">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div class="min-w-0">
                        <div class="mb-3 inline-flex items-center gap-2 rounded-md border border-secondary/25 bg-secondary/10 px-3 py-1 text-xs font-semibold uppercase tracking-normal text-secondary">
                            <span class="size-1.5 rounded-full bg-secondary"></span>
                            Cozinha
                        </div>
                        <h1 class="text-3xl font-bold tracking-normal text-neutral sm:text-4xl">Fila de atendimento</h1>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-base-content/65">
                            Comandas abertas ordenadas por prioridade e chegada.
                        </p>
                    </div>

                    <div class="inline-flex items-center gap-2 rounded-md border border-base-300 bg-base-200/70 px-3 py-2 text-sm text-base-content/65">
                        <flux:icon.queue-list class="size-4 text-primary" />
                        {{ $tickets->count() }} comanda{{ $tickets->count() === 1 ? '' : 's' }}
                    </div>
                </div>
            </section>

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($queueMetrics as $metric)
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

            @if ($tickets->isEmpty())
                <x-card bodyClass="items-center p-10 text-center">
                    <flux:icon.check-circle class="size-8 text-primary" />
                    <h2 class="text-lg font-semibold text-neutral">Nenhuma comanda aguardando preparo</h2>
                    <p class="max-w-md text-sm text-base-content/65">
                        Quando uma comanda for aberta ou enviada para preparo, ela aparece aqui para a cozinha.
                    </p>
                </x-card>
            @else
                <div class="grid gap-4 xl:grid-cols-2">
                    @foreach ($tickets as $ticket)
                        <x-card class="{{ $ticket->priority === 'alta' ? 'border-error/40 ring-1 ring-error/10' : '' }}">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 class="text-xl font-bold text-neutral">Comanda #{{ $ticket->id }}</h2>
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $ticket->priority === 'alta' ? 'bg-error/10 text-error ring-error/20' : 'bg-base-200 text-base-content/70 ring-base-300' }}">
                                            <span class="size-1.5 rounded-full {{ $ticket->priority === 'alta' ? 'bg-error' : 'bg-base-content/35' }}"></span>
                                            {{ $ticket->priority === 'alta' ? 'Alta' : 'Normal' }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-sm text-base-content/65">
                                        {{ $ticket->display_name }}{{ $ticket->table_number ? ' - Mesa '.$ticket->table_number : '' }}
                                    </p>
                                    <p class="text-xs text-base-content/55">
                                        Chegou em {{ optional($ticket->opened_at)->format('d/m/Y H:i') ?? '-' }}
                                    </p>
                                </div>

                                @if ($ticket->status === 'aberta')
                                    <x-form :action="route('ticket-list.start-preparation', $ticket)" post>
                                        <x-primary-button type="submit" class="btn-sm gap-2 whitespace-nowrap">
                                            <flux:icon.play class="size-4" />
                                            Pegar comanda
                                        </x-primary-button>
                                    </x-form>
                                @else
                                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full bg-info/10 px-3 py-1 text-xs font-semibold text-info ring-1 ring-info/20">
                                        <span class="size-1.5 rounded-full bg-info"></span>
                                        Em andamento
                                    </span>
                                @endif
                            </div>

                            <div class="grid grid-cols-3 gap-3 text-sm">
                                <div class="rounded-md bg-base-200/70 p-3">
                                    <span class="text-xs text-base-content/50">Itens</span>
                                    <strong class="mt-1 block text-base text-neutral">{{ $ticket->items->count() }}</strong>
                                </div>
                                <div class="rounded-md bg-base-200/70 p-3">
                                    <span class="text-xs text-base-content/50">Pendentes</span>
                                    <strong class="mt-1 block text-base text-neutral">{{ $ticket->items->where('status', 'pendente')->count() }}</strong>
                                </div>
                                <div class="rounded-md bg-base-200/70 p-3">
                                    <span class="text-xs text-base-content/50">Entregues</span>
                                    <strong class="mt-1 block text-base text-neutral">{{ $ticket->items->where('status', 'entregue')->count() }}</strong>
                                </div>
                            </div>

                            <div class="divide-y divide-base-300/70 rounded-lg border border-base-300/80">
                                @foreach ($ticket->items as $item)
                                    <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="min-w-0">
                                            <p class="font-semibold text-neutral">{{ $item->quantity }}x {{ $item->product_name }}</p>
                                            @if ($item->notes)
                                                <p class="mt-1 text-xs text-base-content/60">{{ $item->notes }}</p>
                                            @endif
                                        </div>

                                        <div class="flex shrink-0 flex-wrap items-center gap-2">
                                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $itemMeta[$item->status]['badge'] ?? 'bg-base-200 text-base-content ring-base-300' }}">
                                                <span class="size-1.5 rounded-full {{ $itemMeta[$item->status]['dot'] ?? 'bg-base-content/40' }}"></span>
                                                {{ $itemLabels[$item->status] ?? $item->status }}
                                            </span>

                                            @if ($item->status !== 'entregue')
                                                <x-form :action="route('ticket-items.deliver', $item)" post>
                                                    <x-secondary-button type="submit" class="btn-sm gap-2">
                                                        <flux:icon.check class="size-4" />
                                                        Entregar
                                                    </x-secondary-button>
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
