@php
    $statusLabels = [
        'disponivel' => 'Disponível',
        'ocupada' => 'Ocupada',
        'reservada' => 'Reservada',
        'manutencao' => 'Manutenção',
    ];

    $statusMeta = [
        'disponivel' => [
            'badge' => 'bg-success/10 text-success ring-success/20',
            'dot' => 'bg-success',
            'icon' => 'check-circle',
        ],
        'ocupada' => [
            'badge' => 'bg-error/10 text-error ring-error/20',
            'dot' => 'bg-error',
            'icon' => 'x-circle',
        ],
        'reservada' => [
            'badge' => 'bg-warning/15 text-warning ring-warning/30',
            'dot' => 'bg-warning',
            'icon' => 'calendar-days',
        ],
        'manutencao' => [
            'badge' => 'bg-base-200 text-base-content/70 ring-base-300',
            'dot' => 'bg-base-content/35',
            'icon' => 'wrench-screwdriver',
        ],
    ];

    $tableItems = collect($tables->items());
    $tableMetrics = [
        [
            'label' => 'Mesas nesta pagina',
            'value' => $tableItems->count(),
            'description' => 'Cadastro operacional',
            'icon' => 'table-cells',
            'accent' => 'text-primary bg-primary/10 ring-primary/15',
        ],
        [
            'label' => 'Disponiveis',
            'value' => $tableItems->where('status', 'disponivel')->count(),
            'description' => 'Prontas para nova comanda',
            'icon' => 'check-circle',
            'accent' => 'text-success bg-success/10 ring-success/15',
        ],
        [
            'label' => 'Ocupadas',
            'value' => $tableItems->where('status', 'ocupada')->count(),
            'description' => 'Com atendimento ativo',
            'icon' => 'x-circle',
            'accent' => 'text-error bg-error/10 ring-error/15',
        ],
        [
            'label' => 'Capacidade',
            'value' => $tableItems->sum('capacity'),
            'description' => 'Lugares mapeados',
            'icon' => 'users',
            'accent' => 'text-secondary bg-secondary/10 ring-secondary/15',
        ],
    ];
@endphp

<x-layouts::app :title="__('Mesas')">
    <div class="min-h-full text-base-content">
        <div class="mx-auto flex w-full max-w-[100rem] flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8 2xl:px-10">
            <section class="rounded-lg border border-base-300/80 bg-base-100/90 p-5 shadow-sm backdrop-blur sm:p-6">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div class="min-w-0">
                        <div class="mb-3 inline-flex items-center gap-2 rounded-md border border-secondary/25 bg-secondary/10 px-3 py-1 text-xs font-semibold uppercase tracking-normal text-secondary">
                            <span class="size-1.5 rounded-full bg-secondary"></span>
                            Admin
                        </div>
                        <h1 class="text-3xl font-bold tracking-normal text-neutral sm:text-4xl">Mesas</h1>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-base-content/65">
                            Cadastre as mesas que poderão ser selecionadas ao abrir comandas.
                        </p>
                    </div>

                    <x-link-button href="{{ route('restaurant-tables.create') }}" class="min-h-11 gap-2 whitespace-nowrap px-4">
                        <flux:icon.plus class="size-4" />
                        Nova mesa
                    </x-link-button>
                </div>
            </section>

            @if (session('status'))
                <div class="alert alert-success rounded-lg border border-success/20 bg-success/10 text-success">
                    <flux:icon.check-circle class="size-5" />
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($tableMetrics as $metric)
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
                        <h2 class="text-lg font-semibold text-neutral">Mapa de mesas</h2>
                        <p class="text-sm text-base-content/55">Status, capacidade e vinculos ativos por mesa.</p>
                    </div>

                    <div class="inline-flex items-center gap-2 rounded-md border border-base-300 bg-base-200/70 px-3 py-2 text-sm text-base-content/65">
                        <flux:icon.table-cells class="size-4 text-primary" />
                        {{ $tableItems->count() }} registro{{ $tableItems->count() === 1 ? '' : 's' }}
                    </div>
                </div>

                @if ($tables->isEmpty())
                    <div class="flex flex-col items-center gap-4 p-10 text-center">
                        <flux:icon.table-cells class="size-8 text-primary" />
                        <div>
                            <h2 class="text-lg font-semibold text-neutral">Nenhuma mesa cadastrada</h2>
                            <p class="mt-1 max-w-md text-sm text-base-content/65">
                                Cadastre as mesas para que elas aparecam na abertura de comandas conforme disponibilidade.
                            </p>
                        </div>
                        <x-link-button href="{{ route('restaurant-tables.create') }}" class="gap-2">
                            <flux:icon.plus class="size-4" />
                            Cadastrar mesa
                        </x-link-button>
                    </div>
                @else
                    <div class="grid gap-3 p-4 md:hidden">
                        @foreach ($tables as $table)
                            <a href="{{ route('restaurant-tables.edit', $table) }}" class="rounded-lg border border-base-300/80 bg-base-100 p-4 shadow-sm" wire:navigate>
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-normal text-base-content/45">Mesa</p>
                                        <p class="mt-1 text-xl font-bold text-neutral">{{ $table->identifier }}</p>
                                        <p class="text-sm text-base-content/60">{{ $table->capacity }} lugares</p>
                                    </div>

                                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusMeta[$table->status]['badge'] ?? 'bg-base-200 text-base-content ring-base-300' }}">
                                        <span class="size-1.5 rounded-full {{ $statusMeta[$table->status]['dot'] ?? 'bg-base-content/40' }}"></span>
                                        {{ $statusLabels[$table->status] ?? $table->status }}
                                    </span>
                                </div>

                                <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                                    <div class="rounded-md bg-base-200/70 p-3">
                                        <span class="text-xs text-base-content/50">Comandas</span>
                                        <strong class="mt-1 block text-base text-neutral">{{ $table->open_tickets_count }}</strong>
                                    </div>
                                    <div class="rounded-md bg-base-200/70 p-3">
                                        <span class="text-xs text-base-content/50">Reservas</span>
                                        <strong class="mt-1 block text-base text-neutral">{{ $table->active_reservations_count }}</strong>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="hidden overflow-x-auto md:block">
                        <table class="min-w-full text-sm">
                            <thead class="bg-base-200/70 text-left text-xs font-semibold uppercase tracking-normal text-base-content/55">
                                <tr>
                                    <th class="px-5 py-4">Mesa</th>
                                    <th class="px-5 py-4">Capacidade</th>
                                    <th class="px-5 py-4">Status</th>
                                    <th class="px-5 py-4">Comandas abertas</th>
                                    <th class="px-5 py-4">Reservas confirmadas</th>
                                    <th class="px-5 py-4 text-right">Acoes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-base-300/70">
                                @foreach ($tables as $table)
                                    <tr class="transition hover:bg-primary/5">
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-3">
                                                <flux:icon.table-cells class="size-6 text-primary" />
                                                <div>
                                                    <p class="font-semibold text-neutral">Mesa {{ $table->identifier }}</p>
                                                    <p class="text-xs text-base-content/50">ID operacional</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-base-content/70">{{ $table->capacity }} lugares</td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusMeta[$table->status]['badge'] ?? 'bg-base-200 text-base-content ring-base-300' }}">
                                                <span class="size-1.5 rounded-full {{ $statusMeta[$table->status]['dot'] ?? 'bg-base-content/40' }}"></span>
                                                {{ $statusLabels[$table->status] ?? $table->status }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 font-semibold text-neutral">{{ $table->open_tickets_count }}</td>
                                        <td class="px-5 py-4 font-semibold text-neutral">{{ $table->active_reservations_count }}</td>
                                        <td class="px-5 py-4 text-right">
                                            <a href="{{ route('restaurant-tables.edit', $table) }}" class="btn btn-primary btn-soft btn-sm gap-2" wire:navigate>
                                                <flux:icon.pencil-square class="size-4" />
                                                Editar
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($tables->hasPages())
                        <div class="border-t border-base-300/80 p-4">
                            {{ $tables->links() }}
                        </div>
                    @endif
                @endif
            </x-card>
        </div>
    </div>
</x-layouts::app>
