@php
    $money = fn ($value) => 'R$ '.number_format((float) $value, 2, ',', '.');
    $statusLabels = [
        'aberta' => 'Aberta',
        'em_andamento' => 'Em andamento',
        'fechada' => 'Fechada',
        'paga' => 'Paga',
        'cancelada' => 'Cancelada',
    ];
    $statusMeta = [
        'aberta' => [
            'badge' => 'bg-primary/10 text-primary ring-primary/20',
            'dot' => 'bg-primary',
            'icon' => 'clipboard-document-list',
        ],
        'em_andamento' => [
            'badge' => 'bg-info/10 text-info ring-info/20',
            'dot' => 'bg-info',
            'icon' => 'clock',
        ],
        'fechada' => [
            'badge' => 'bg-warning/15 text-warning ring-warning/30',
            'dot' => 'bg-warning',
            'icon' => 'lock-closed',
        ],
        'paga' => [
            'badge' => 'bg-success/10 text-success ring-success/20',
            'dot' => 'bg-success',
            'icon' => 'check-circle',
        ],
        'cancelada' => [
            'badge' => 'bg-error/10 text-error ring-error/20',
            'dot' => 'bg-error',
            'icon' => 'x-circle',
        ],
    ];
    $priorityMeta = [
        'alta' => 'bg-error/10 text-error ring-error/20',
        'normal' => 'bg-base-200 text-base-content/70 ring-base-300',
    ];

    $ticketItems = collect($tickets->items());
    $activeCount = $ticketItems->whereIn('status', ['aberta', 'em_andamento', 'fechada'])->count();
    $ticketMetrics = [
        [
            'label' => 'Comandas nesta pagina',
            'value' => $ticketItems->count(),
            'description' => 'Registros recentes',
            'icon' => 'ticket',
            'accent' => 'text-primary bg-primary/10 ring-primary/15',
        ],
        [
            'label' => 'Em Andamento',
            'value' => $activeCount,
            'description' => 'Abertas ou aguardando fluxo',
            'icon' => 'clock',
            'accent' => 'text-info bg-info/10 ring-info/15',
        ],
        [
            'label' => 'Pagas',
            'value' => $ticketItems->where('status', 'paga')->count(),
            'description' => 'Finalizadas na lista',
            'icon' => 'check-circle',
            'accent' => 'text-success bg-success/10 ring-success/15',
        ],
        [
            'label' => 'Total listado',
            'value' => $money($ticketItems->sum('total_amount')),
            'description' => 'Soma das comandas visiveis',
            'icon' => 'banknotes',
            'accent' => 'text-secondary bg-secondary/10 ring-secondary/15',
        ],
    ];
@endphp

<x-layouts::app :title="__('Comandas')">
    <div class="min-h-full text-base-content">
        <div class="mx-auto flex w-full max-w-[100rem] flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8 2xl:px-10">
            <section class="rounded-lg border border-base-300/80 bg-base-100/90 p-5 shadow-sm backdrop-blur sm:p-6">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div class="min-w-0">
                        <div class="mb-3 inline-flex items-center gap-2 rounded-md border border-primary/25 bg-primary/10 px-3 py-1 text-xs font-semibold uppercase tracking-normal text-primary">
                            <span class="size-1.5 rounded-full bg-primary"></span>
                            Tickets
                        </div>
                        <h1 class="text-3xl font-bold tracking-normal text-neutral sm:text-4xl">Comandas</h1>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-base-content/65">
                            Consulte as comandas cadastradas, seus status, valores e quantidade de itens.
                        </p>
                    </div>

                    <x-link-button href="{{ route('ticket-list.create') }}" class="min-h-11 gap-2 whitespace-nowrap px-4">
                        <flux:icon.plus class="size-4" />
                        Nova comanda
                    </x-link-button>
                </div>
            </section>

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($ticketMetrics as $metric)
                    <article class="rounded-lg border border-base-300/80 bg-base-100 p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-base-content/60">{{ $metric['label'] }}</p>
                                <p class="mt-2 truncate text-3xl font-bold tracking-normal text-neutral sm:text-4xl">{{ $metric['value'] }}</p>
                            </div>

                            <div class="grid size-11 shrink-0 place-items-center rounded-lg ring-1 {{ $metric['accent'] }}">
                                <flux:icon :name="$metric['icon']" class="size-5" />
                            </div>
                        </div>

                        <p class="mt-4 border-t border-base-300/70 pt-3 text-sm text-base-content/60">{{ $metric['description'] }}</p>
                    </article>
                @endforeach
            </section>

            <x-card bodyClass="p-0">
                <div class="flex flex-col gap-3 border-b border-base-300/80 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                    <div>
                        <h2 class="text-lg font-semibold text-neutral">Lista de comandas</h2>
                        <p class="text-sm text-base-content/55">Comandas mais recentes, prioridades e valores.</p>
                    </div>

                    <div class="inline-flex items-center gap-2 rounded-md border border-base-300 bg-base-200/70 px-3 py-2 text-sm text-base-content/65">
                        <flux:icon.ticket class="size-4 text-primary" />
                        {{ $ticketItems->count() }} registro{{ $ticketItems->count() === 1 ? '' : 's' }}
                    </div>
                </div>

                @if ($tickets->isEmpty())
                    <div class="flex flex-col items-center gap-4 p-10 text-center">
                        <div class="grid size-12 place-items-center rounded-lg bg-primary/10 text-primary ring-1 ring-primary/15">
                            <flux:icon.ticket class="size-6" />
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-neutral">Nenhuma comanda cadastrada</h2>
                            <p class="mt-1 max-w-md text-sm text-base-content/65">
                                Cadastre a primeira comanda para acompanhar atendimento, status e valores.
                            </p>
                        </div>
                        <x-link-button href="{{ route('ticket-list.create') }}" class="gap-2">
                            <flux:icon.plus class="size-4" />
                            Cadastrar comanda
                        </x-link-button>
                    </div>
                @else
                    <div class="grid gap-3 p-4 md:hidden">
                        @foreach ($tickets as $ticket)
                            <a href="{{ route('ticket-list.show', $ticket) }}" class="rounded-lg border border-base-300/80 bg-base-100 p-4 shadow-sm" wire:navigate>
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold uppercase tracking-normal text-base-content/45">Comanda</p>
                                        <p class="mt-1 text-xl font-bold text-neutral">#{{ $ticket->id }}</p>
                                        <p class="truncate text-sm text-base-content/60">
                                            {{ $ticket->display_name }}{{ $ticket->table_number ? ' - Mesa '.$ticket->table_number : '' }}
                                        </p>
                                    </div>
                                    <span class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusMeta[$ticket->status]['badge'] ?? 'bg-base-200 text-base-content ring-base-300' }}">
                                        <span class="size-1.5 rounded-full {{ $statusMeta[$ticket->status]['dot'] ?? 'bg-base-content/40' }}"></span>
                                        {{ $statusLabels[$ticket->status] ?? $ticket->status }}
                                    </span>
                                </div>
                                <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                                    <div class="rounded-md bg-base-200/70 p-3">
                                        <span class="text-xs text-base-content/50">Itens</span>
                                        <strong class="mt-1 block text-base text-neutral">{{ $ticket->items_count }}</strong>
                                    </div>
                                    <div class="rounded-md bg-base-200/70 p-3">
                                        <span class="text-xs text-base-content/50">Total</span>
                                        <strong class="mt-1 block text-base text-neutral">{{ $money($ticket->total_amount) }}</strong>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="hidden overflow-x-auto md:block">
                        <table class="min-w-full text-sm">
                            <thead class="bg-base-200/70 text-left text-xs font-semibold uppercase tracking-normal text-base-content/55">
                                <tr>
                                    <th class="px-5 py-4">Comanda</th>
                                    <th class="px-5 py-4">Cliente</th>
                                    <th class="px-5 py-4">Mesa</th>
                                    <th class="px-5 py-4">Status</th>
                                    <th class="px-5 py-4">Prioridade</th>
                                    <th class="px-5 py-4">Itens</th>
                                    <th class="px-5 py-4">Total</th>
                                    <th class="px-5 py-4">Abertura</th>
                                    <th class="px-5 py-4 text-right">Acoes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-base-300/70">
                                @foreach ($tickets as $ticket)
                                    <tr class="transition hover:bg-primary/5">
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="grid size-10 place-items-center rounded-lg bg-primary/10 text-primary ring-1 ring-primary/15">
                                                    <flux:icon.ticket class="size-5" />
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-neutral">#{{ $ticket->id }}</p>
                                                    <p class="text-xs text-base-content/50">Comanda</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-base-content/70">{{ $ticket->display_name }}</td>
                                        <td class="px-5 py-4 font-semibold text-neutral">{{ $ticket->table_number ?: '-' }}</td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusMeta[$ticket->status]['badge'] ?? 'bg-base-200 text-base-content ring-base-300' }}">
                                                <span class="size-1.5 rounded-full {{ $statusMeta[$ticket->status]['dot'] ?? 'bg-base-content/40' }}"></span>
                                                {{ $statusLabels[$ticket->status] ?? $ticket->status }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $priorityMeta[$ticket->priority] ?? $priorityMeta['normal'] }}">
                                                <span class="size-1.5 rounded-full {{ $ticket->priority === 'alta' ? 'bg-error' : 'bg-base-content/35' }}"></span>
                                                {{ $ticket->priority === 'alta' ? 'Alta' : 'Normal' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 font-semibold text-neutral">{{ $ticket->items_count }}</td>
                                        <td class="px-5 py-4 font-semibold text-neutral">{{ $money($ticket->total_amount) }}</td>
                                        <td class="px-5 py-4 text-base-content/70">{{ optional($ticket->opened_at)->format('d/m/Y H:i') ?? '-' }}</td>
                                        <td class="px-5 py-4 text-right">
                                            <a href="{{ route('ticket-list.show', $ticket) }}" class="btn btn-primary btn-soft btn-sm gap-2">
                                                <flux:icon.eye class="size-4" />
                                                Detalhes
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($tickets->hasPages())
                        <div class="border-t border-base-300/80 p-4">
                            {{ $tickets->links() }}
                        </div>
                    @endif
                @endif
            </x-card>
        </div>
    </div>
</x-layouts::app>
