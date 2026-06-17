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
    $statusBadges = [
        'aberta' => 'badge-primary',
        'em_andamento' => 'badge-info',
        'fechada' => 'badge-warning',
        'paga' => 'badge-success',
        'cancelada' => 'badge-error',
    ];
    $statusProgress = [
        'aberta' => 'progress-primary',
        'em_andamento' => 'progress-info',
        'fechada' => 'progress-warning',
        'paga' => 'progress-success',
        'cancelada' => 'progress-error',
    ];
@endphp

<x-layouts::app :title="__('Dashboard')">
    <div class="min-h-full text-base-content">
        <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8">
            <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="badge badge-secondary badge-outline mb-3">Central de comandas</div>
                    <h1 class="text-2xl font-bold tracking-normal text-base-content/70 sm:text-3xl">Dashboard de Comandas</h1>
                    <p class="mt-2 max-w-2xl text-sm text-base-content/70">
                        Acompanhe comandas abertas, pagamentos e movimentações cadastradas no sistema.
                    </p>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row">
                    <x-form action="{{ route('dashboard') }}" class="flex-row gap-2">
                        <input type="hidden" name="status" value="{{ $selectedStatus }}">
                        <x-text-input name="search" type="search" value="{{ $search }}" placeholder="Buscar comanda" class="min-h-11" />
                        <x-primary-button type="submit" class="min-h-11">Buscar</x-primary-button>
                    </x-form>

                    <x-link-button href="{{ route('ticket-list.create') }}" class="min-h-11">Nova comanda</x-link-button>
                </div>
            </section>

            <section class="stats stats-vertical overflow-hidden rounded-lg border border-base-300 bg-base-100 shadow-sm lg:stats-horizontal">
                <div class="stat">
                    <div class="stat-title">Comandas abertas</div>
                    <div class="stat-value text-primary">{{ $metrics['open'] }}</div>
                    <div class="stat-desc">{{ $money($metrics['openAmount']) }} em aberto</div>
                </div>

                <div class="stat">
                    <div class="stat-title">Fechadas</div>
                    <div class="stat-value text-warning">{{ $metrics['closed'] }}</div>
                    <div class="stat-desc">Aguardando pagamento</div>
                </div>

                <div class="stat">
                    <div class="stat-title">Pagas</div>
                    <div class="stat-value text-success">{{ $metrics['paid'] }}</div>
                    <div class="stat-desc">{{ $money($metrics['revenueToday']) }} recebido hoje</div>
                </div>

                <div class="stat">
                    <div class="stat-title">Canceladas</div>
                    <div class="stat-value text-error">{{ $metrics['canceled'] }}</div>
                    <div class="stat-desc">Registros preservados</div>
                </div>
            </section>

            <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]">
                <div class="flex flex-col gap-4">
                    <x-card title="Fila de comandas" description="Tickets cadastrados, filtrados por status e busca.">
                        <div class="join flex flex-wrap">
                            @foreach ($statusLabels as $status => $label)
                                <a
                                    href="{{ route('dashboard', array_filter(['status' => $status, 'search' => $search])) }}"
                                    class="btn btn-sm join-item {{ $selectedStatus === $status ? 'btn-soft btn-primary' : 'btn-ghost' }}"
                                >
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </x-card>

                    @if ($tickets->isEmpty())
                        <x-card class="border-dashed" bodyClass="items-center p-8 text-center">
                            <div class="badge badge-outline badge-primary">Sem comandas</div>
                            <h2 class="text-lg font-semibold text-neutral">Nenhum ticket encontrado</h2>
                            <p class="max-w-md text-sm text-base-content/65">
                                Quando as comandas forem cadastradas, elas aparecem aqui com status, valores e quantidade de itens.
                            </p>
                            <x-link-button href="{{ route('ticket-list.create') }}">Cadastrar comanda</x-link-button>
                        </x-card>
                    @else
                        <div class="grid gap-4 md:grid-cols-2 2xl:grid-cols-3">
                            @foreach ($tickets as $ticket)
                                <x-card class="border-primary/15">
                                    <div class="grid grid-cols-[minmax(0,1fr)_auto] items-start gap-3">
                                        <div class="min-w-0">
                                            <h3 class="card-title text-base text-neutral">Comanda #{{ $ticket->id }}</h3>
                                            <p class="text-sm text-base-content/60">
                                                {{ $ticket->display_name }}
                                                @if ($ticket->table_number)
                                                    - Mesa {{ $ticket->table_number }}
                                                @endif
                                            </p>
                                        </div>

                                        <div class="badge whitespace-nowrap px-3 {{ $statusBadges[$ticket->status] ?? 'badge-neutral' }}">
                                            {{ $statusLabels[$ticket->status] ?? $ticket->status }}
                                        </div>
                                    </div>

                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between"><span>Itens</span><strong>{{ $ticket->items_count }}</strong></div>
                                        <div class="flex justify-between"><span>Total</span><strong>{{ $money($ticket->total_amount) }}</strong></div>
                                        <div class="flex justify-between">
                                            <span>Aberta em</span>
                                            <strong>{{ optional($ticket->opened_at)->format('d/m H:i') ?? '-' }}</strong>
                                        </div>
                                    </div>

                                    <div class="card-actions justify-end">
                                        <x-secondary-button type="button" class="btn-sm">Detalhes</x-secondary-button>
                                    </div>
                                </x-card>
                            @endforeach
                        </div>
                    @endif
                </div>

                <aside class="flex flex-col gap-4">
                    <x-card title="Resumo por status">
                        <div class="space-y-4">
                            @foreach (['aberta', 'em_andamento', 'fechada', 'paga', 'cancelada'] as $status)
                                <div>
                                    <div class="mb-1 flex justify-between text-sm">
                                        <span>{{ $statusLabels[$status] }}</span>
                                        <strong>{{ (int) $statusCounts->get($status, 0) }}</strong>
                                    </div>
                                    <progress
                                        class="progress {{ $statusProgress[$status] }}"
                                        value="{{ $statusPercentages->get($status, 0) }}"
                                        max="100"
                                    ></progress>
                                </div>
                            @endforeach
                        </div>
                    </x-card>

                    <x-card title="Atividade recente">
                        @if ($recentTickets->isEmpty())
                            <p class="text-sm text-base-content/65">Nenhuma movimentação registrada ainda.</p>
                        @else
                            <ul class="timeline timeline-vertical timeline-compact">
                                @foreach ($recentTickets as $ticket)
                                    <li>
                                        @if (! $loop->first)
                                            <hr class="bg-base-300" />
                                        @endif

                                        <div class="timeline-middle h-3 w-3 rounded-full bg-primary"></div>
                                        <div class="timeline-end {{ $loop->last ? '' : 'mb-4' }}">
                                            <p class="text-sm font-semibold">
                                                Comanda #{{ $ticket->id }} - {{ $statusLabels[$ticket->status] ?? $ticket->status }}
                                            </p>
                                            <p class="text-xs text-base-content/55">
                                                Atualizada em {{ optional($ticket->updated_at)->format('d/m/Y H:i') ?? '-' }}
                                            </p>
                                        </div>

                                        @if (! $loop->last)
                                            <hr class="bg-base-300" />
                                        @endif
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
