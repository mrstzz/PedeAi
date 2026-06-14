@php
    $money = fn ($value) => 'R$ '.number_format((float) $value, 2, ',', '.');
    $statusLabels = [
        'todos' => 'Todas',
        'aberta' => 'Abertas',
        'em_andamento' => 'Em andamento',
        'fechada' => 'Fechadas',
        'paga' => 'Pagas',
        'cancelada' => 'Canceladas',
    ];
    $statusMeta = [
        'aberta' => [
            'badge' => 'bg-primary/10 text-primary ring-primary/20',
            'bar' => 'bg-primary',
            'dot' => 'bg-primary',
            'icon' => 'clipboard-document-list',
        ],
        'em_andamento' => [
            'badge' => 'bg-info/10 text-info ring-info/20',
            'bar' => 'bg-info',
            'dot' => 'bg-info',
            'icon' => 'clock',
        ],
        'fechada' => [
            'badge' => 'bg-warning/15 text-warning ring-warning/30',
            'bar' => 'bg-warning',
            'dot' => 'bg-warning',
            'icon' => 'lock-closed',
        ],
        'paga' => [
            'badge' => 'bg-success/10 text-success ring-success/20',
            'bar' => 'bg-success',
            'dot' => 'bg-success',
            'icon' => 'check-circle',
        ],
        'cancelada' => [
            'badge' => 'bg-error/10 text-error ring-error/20',
            'bar' => 'bg-error',
            'dot' => 'bg-error',
            'icon' => 'x-circle',
        ],
    ];
    $filterCounts = collect($statusLabels)->mapWithKeys(fn ($label, $status) => [
        $status => $status === 'todos' ? (int) $statusCounts->sum() : (int) $statusCounts->get($status, 0),
    ]);
    $activeTickets = (int) $metrics['open'] + (int) $metrics['inProgress'] + (int) $metrics['closed'];
    $metricCards = [
        [
            'label' => 'Em andamento',
            'value' => $activeTickets,
            'description' => 'Comandas ativas no salão',
            'icon' => 'clipboard-document-list',
            'accent' => 'text-primary bg-primary/10 ring-primary/15',
        ],
        [
            'label' => 'Em aberto',
            'value' => $metrics['open'],
            'description' => $money($metrics['openAmount']).' pendentes',
            'icon' => 'clock',
            'accent' => 'text-info bg-info/10 ring-info/15',
        ],
        [
            'label' => 'Pagas',
            'value' => $metrics['paid'],
            'description' => $money($metrics['revenueToday']).' recebido hoje',
            'icon' => 'banknotes',
            'accent' => 'text-success bg-success/10 ring-success/15',
        ],
        [
            'label' => 'Canceladas',
            'value' => $metrics['canceled'],
            'description' => 'Registros preservados',
            'icon' => 'x-circle',
            'accent' => 'text-error bg-error/10 ring-error/15',
        ],
    ];
@endphp

<x-layouts::app :title="__('Dashboard')">
    <div class="min-h-full text-base-content">
        <div class="mx-auto flex w-full max-w-[100rem] flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8 2xl:px-10">
            <section class="rounded-lg border border-base-300/80 bg-base-100/90 p-5 shadow-sm backdrop-blur sm:p-6">
                <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
                    <div class="min-w-0">
                        <div class="mb-3 inline-flex items-center gap-2 rounded-md border border-secondary/25 bg-secondary/10 px-3 py-1 text-xs font-semibold uppercase tracking-normal text-secondary">
                            <span class="size-1.5 rounded-full bg-secondary"></span>
                            Central de comandas
                        </div>
                        <h1 class="text-3xl font-bold tracking-normal text-neutral sm:text-4xl">Dashboard de Comandas</h1>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-base-content/65">
                            Acompanhe comandas abertas, pagamentos e movimentações cadastradas no sistema.
                        </p>
                    </div>

                    <div class="flex w-full flex-col gap-3 sm:flex-row xl:w-auto">
                        <x-form action="{{ route('dashboard') }}" class="w-full flex-row gap-2 sm:w-auto">
                            <input type="hidden" name="status" value="{{ $selectedStatus }}">
                            <label class="input input-bordered flex min-h-11 w-full items-center gap-2 bg-base-100 shadow-inner sm:w-72">
                                <flux:icon.magnifying-glass class="size-4 shrink-0 text-base-content/40" />
                                <input name="search" type="search" value="{{ $search }}" placeholder="Buscar comanda" class="grow bg-transparent text-sm outline-none" />
                            </label>
                            <x-primary-button type="submit" class="min-h-11 gap-2 px-4">
                                <flux:icon.magnifying-glass class="size-4" />
                                Buscar
                            </x-primary-button>
                        </x-form>

                        <x-link-button href="{{ route('ticket-list.create') }}" class="min-h-11 gap-2 whitespace-nowrap px-4">
                            <flux:icon.plus class="size-4" />
                            Nova comanda
                        </x-link-button>
                    </div>
                </div>
            </section>

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($metricCards as $card)
                    <article class="rounded-lg border border-base-300/80 bg-base-100 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-base-content/60">{{ $card['label'] }}</p>
                                <p class="mt-2 text-4xl font-bold tracking-normal text-neutral">{{ $card['value'] }}</p>
                            </div>

                            <div class="grid size-11 place-items-center rounded-lg ring-1 {{ $card['accent'] }}">
                                <flux:icon :name="$card['icon']" class="size-5" />
                            </div>
                        </div>

                        <p class="mt-4 border-t border-base-300/70 pt-3 text-sm text-base-content/60">{{ $card['description'] }}</p>
                    </article>
                @endforeach
            </section>

            <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_24rem]">
                <div class="flex flex-col gap-4">
                    <x-card>
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-neutral">Fila de comandas</h2>
                                <p class="text-sm text-base-content/55">Tickets cadastrados, filtrados por status e busca.</p>
                            </div>

                            <div class="inline-flex items-center gap-2 rounded-md border border-base-300 bg-base-200/70 px-3 py-2 text-sm text-base-content/65">
                                <flux:icon.funnel class="size-4 text-primary" />
                                {{ $filterCounts->get($selectedStatus, 0) }} resultado{{ $filterCounts->get($selectedStatus, 0) === 1 ? '' : 's' }}
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            @foreach ($statusLabels as $status => $label)
                                <a
                                    href="{{ route('dashboard', array_filter(['status' => $status, 'search' => $search])) }}"
                                    class="btn btn-sm h-10 min-h-10 rounded-md border px-3 {{ $selectedStatus === $status ? 'border-primary/20 bg-primary text-primary-content hover:bg-primary/90' : 'border-base-300 bg-base-100 text-base-content/70 hover:border-primary/30 hover:bg-primary/5 hover:text-primary' }}"
                                >
                                    {{ $label }}
                                    <span class="rounded bg-base-content/10 px-1.5 py-0.5 text-xs font-semibold">{{ $filterCounts->get($status, 0) }}</span>
                                </a>
                            @endforeach
                        </div>
                    </x-card>

                    @if ($tickets->isEmpty())
                        <x-card class="border-dashed" bodyClass="items-center p-8 text-center">
                            <div class="grid size-12 place-items-center rounded-lg bg-primary/10 text-primary ring-1 ring-primary/15">
                                <flux:icon.clipboard-document-list class="size-6" />
                            </div>
                            <h2 class="text-lg font-semibold text-neutral">Nenhum ticket encontrado</h2>
                            <p class="max-w-md text-sm text-base-content/65">
                                Quando as comandas forem cadastradas, elas aparecem aqui com status, valores e quantidade de itens.
                            </p>
                            <x-link-button href="{{ route('ticket-list.create') }}" class="gap-2">
                                <flux:icon.plus class="size-4" />
                                Cadastrar comanda
                            </x-link-button>
                        </x-card>
                    @else
                        <div class="grid gap-4 md:grid-cols-2 2xl:grid-cols-3">
                            @foreach ($tickets as $ticket)
                                <x-card class="group border-base-300/80 transition hover:-translate-y-0.5 hover:border-primary/25 hover:shadow-md">
                                    <div class="grid grid-cols-[minmax(0,1fr)_auto] items-start gap-3">
                                        <div class="min-w-0">
                                            <p class="text-xs font-semibold uppercase tracking-normal text-base-content/45">Comanda</p>
                                            <h3 class="mt-1 text-xl font-bold text-neutral">#{{ $ticket->id }}</h3>
                                            <p class="mt-1 truncate text-sm text-base-content/60">
                                                {{ $ticket->display_name }}
                                                @if ($ticket->table_number)
                                                    - Mesa {{ $ticket->table_number }}
                                                @endif
                                            </p>
                                        </div>

                                        <div class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusMeta[$ticket->status]['badge'] ?? 'bg-base-200 text-base-content ring-base-300' }}">
                                            <span class="size-1.5 rounded-full {{ $statusMeta[$ticket->status]['dot'] ?? 'bg-base-content/40' }}"></span>
                                            {{ $statusLabels[$ticket->status] ?? $ticket->status }}
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div class="rounded-md bg-base-200/70 p-3">
                                            <p class="text-xs text-base-content/50">Itens</p>
                                            <strong class="mt-1 block text-base text-neutral">{{ $ticket->items_count }}</strong>
                                        </div>
                                        <div class="rounded-md bg-base-200/70 p-3">
                                            <p class="text-xs text-base-content/50">Aberta em</p>
                                            <strong class="mt-1 block text-base text-neutral">{{ optional($ticket->opened_at)->format('d/m H:i') ?? '-' }}</strong>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between border-t border-base-300/70 pt-4">
                                        <div>
                                            <p class="text-xs text-base-content/50">Total</p>
                                            <strong class="text-lg text-neutral">{{ $money($ticket->total_amount) }}</strong>
                                        </div>

                                        <a href="{{ route('ticket-list.show', $ticket) }}" class="btn btn-primary btn-soft btn-sm gap-2">
                                            <flux:icon.eye class="size-4" />
                                            Detalhes
                                        </a>
                                    </div>
                                </x-card>
                            @endforeach
                        </div>
                    @endif
                </div>

                <aside class="flex flex-col gap-4">
                    <x-card>
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h2 class="text-base font-semibold text-neutral">Resumo por status</h2>
                                <p class="text-sm text-base-content/55">{{ (int) $statusCounts->sum() }} comandas no total</p>
                            </div>

                            <div class="grid size-10 place-items-center rounded-lg bg-primary/10 text-primary ring-1 ring-primary/15">
                                <flux:icon.clipboard class="size-5" />
                            </div>
                        </div>

                        <div class="space-y-4">
                            @foreach (['aberta', 'em_andamento', 'fechada', 'paga', 'cancelada'] as $status)
                                <div>
                                    <div class="mb-2 flex justify-between text-sm">
                                        <span class="inline-flex items-center gap-2 text-base-content/70">
                                            <span class="size-2 rounded-full {{ $statusMeta[$status]['dot'] }}"></span>
                                            {{ $statusLabels[$status] }}
                                        </span>
                                        <strong>{{ (int) $statusCounts->get($status, 0) }}</strong>
                                    </div>
                                    <div class="h-2 overflow-hidden rounded-full bg-base-300/70">
                                        <div class="h-full rounded-full {{ $statusMeta[$status]['bar'] }}" style="width: {{ $statusPercentages->get($status, 0) }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </x-card>

                    <x-card>
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h2 class="text-base font-semibold text-neutral">Atividade recente</h2>
                                <p class="text-sm text-base-content/55">Últimas movimentações</p>
                            </div>

                            <div class="grid size-10 place-items-center rounded-lg bg-secondary/10 text-secondary ring-1 ring-secondary/15">
                                <flux:icon.bell-alert class="size-5" />
                            </div>
                        </div>

                        @if ($recentTickets->isEmpty())
                            <p class="text-sm text-base-content/65">Nenhuma movimentação registrada ainda.</p>
                        @else
                            <ul class="space-y-4">
                                @foreach ($recentTickets as $ticket)
                                    <li class="relative pl-5 {{ $loop->last ? '' : 'pb-4' }}">
                                        @if (! $loop->last)
                                            <span class="absolute bottom-0 left-[5px] top-4 w-px bg-base-300"></span>
                                        @endif

                                        <span class="absolute left-0 top-1.5 size-2.5 rounded-full ring-4 ring-base-100 {{ $statusMeta[$ticket->status]['dot'] ?? 'bg-primary' }}"></span>
                                        <div class="rounded-md border border-base-300/70 bg-base-100 p-3">
                                            <p class="text-sm font-semibold text-neutral">
                                                Comanda #{{ $ticket->id }} - {{ $statusLabels[$ticket->status] ?? $ticket->status }}
                                            </p>
                                            <p class="text-xs text-base-content/55">
                                                Atualizada em {{ optional($ticket->updated_at)->format('d/m/Y H:i') ?? '-' }}
                                            </p>
                                            <a href="{{ route('ticket-list.show', $ticket) }}" class="mt-2 inline-flex items-center gap-1 text-xs font-semibold text-primary hover:text-primary/80">
                                                Abrir comanda
                                                <flux:icon.chevron-right class="size-3" />
                                            </a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </x-card>
                </aside>
            </section>
        </div>
    </div>
</x-layouts::app>
