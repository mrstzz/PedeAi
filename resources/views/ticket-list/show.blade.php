@php
    $money = fn ($value) => 'R$ '.number_format((float) $value, 2, ',', '.');
    $itemLabels = [
        'pendente' => 'Pendente',
        'em_preparo' => 'Em preparo',
        'entregue' => 'Entregue',
    ];
    $itemTones = [
        'pendente' => 'warning',
        'em_preparo' => 'info',
        'entregue' => 'success',
    ];
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
    $priorityTone = $ticket->priority === 'alta' ? 'error' : 'neutral';
@endphp

<x-layouts::app :title="__('Detalhes da comanda')">
    <div class="min-h-full text-base-content">
        <div class="mx-auto flex w-full max-w-[100rem] flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8 2xl:px-10">
            <x-page-header
                :eyebrow="$ticket->priority === 'alta' ? 'Prioridade alta' : 'Prioridade normal'"
                title="Comanda #{{ $ticket->id }}"
                description="{{ $ticket->display_name }}{{ $ticket->restaurantTable ? ' - Mesa '.$ticket->restaurantTable->identifier : ($ticket->table_number ? ' - Mesa '.$ticket->table_number : '') }}"
                icon="ticket"
            >
                <x-status-pill :label="$statusLabels[$ticket->status] ?? $ticket->status" :tone="$statusTones[$ticket->status] ?? 'neutral'" />
                <x-link-button href="{{ route('ticket-list.index') }}" class="min-h-11 gap-2 whitespace-nowrap px-4">
                    <flux:icon.arrow-left class="size-4" />
                    Voltar
                </x-link-button>
            </x-page-header>

            @if ($ticket->reservation)
                <x-alert-message tone="info">
                    Reserva #{{ $ticket->reservation->id }} - {{ $ticket->reservation->reserved_at->timezone('America/Sao_Paulo')->format('d/m/Y H:i') }}
                </x-alert-message>
            @endif

            @if ($errors->any())
                <x-alert-message tone="error" title="Revise os dados informados">
                    {{ $errors->first() }}
                </x-alert-message>
            @endif

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-metric-card
                    label="Total da comanda"
                    value="{{ $money($ticket->total_amount) }}"
                    description="Valor acumulado dos itens."
                    icon="banknotes"
                    accent="text-secondary bg-secondary/10 ring-secondary/15"
                />
                <x-metric-card
                    label="Itens"
                    :value="$ticket->items->count()"
                    description="Produtos lançados."
                    icon="book-open"
                    accent="text-primary bg-primary/10 ring-primary/15"
                />
                <x-metric-card
                    label="Entregues"
                    :value="$ticket->items->where('status', 'entregue')->count()"
                    description="Itens concluídos."
                    icon="check-circle"
                    accent="text-success bg-success/10 ring-success/15"
                />
                <x-metric-card
                    label="Prioridade"
                    value="{{ $ticket->priority === 'alta' ? 'Alta' : 'Normal' }}"
                    description="Ordem de atenção operacional."
                    icon="exclamation-triangle"
                    accent="{{ $ticket->priority === 'alta' ? 'text-error bg-error/10 ring-error/15' : 'text-info bg-info/10 ring-info/15' }}"
                />
            </section>

            <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_24rem]">
                <x-card bodyClass="p-0">
                    <div class="flex flex-col gap-3 border-b border-base-300/80 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                        <div>
                            <h2 class="text-lg font-semibold text-neutral">Itens da comanda</h2>
                            <p class="text-sm text-base-content/55">Produtos, quantidades, status e subtotais.</p>
                        </div>

                        <x-status-pill :label="$ticket->priority === 'alta' ? 'Alta' : 'Normal'" :tone="$priorityTone" />
                    </div>

                    <div class="grid gap-3 p-4 md:hidden">
                        @foreach ($ticket->items as $item)
                            <div class="rounded-lg border border-base-300/80 bg-base-100 p-4 shadow-sm">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-neutral">{{ $item->product_name }}</p>
                                        <p class="text-sm text-base-content/60">Qtd. {{ $item->quantity }}</p>
                                    </div>
                                    <x-status-pill :label="$itemLabels[$item->status] ?? $item->status" :tone="$itemTones[$item->status] ?? 'neutral'" />
                                </div>
                                @if ($item->notes)
                                    <p class="mt-2 text-xs text-base-content/60">{{ $item->notes }}</p>
                                @endif
                                <p class="mt-3 text-right font-semibold text-neutral">{{ $money($item->subtotal) }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="hidden overflow-x-auto md:block">
                        <table class="min-w-full text-sm">
                            <thead class="bg-base-200/70 text-left text-xs font-semibold uppercase tracking-normal text-base-content/55">
                                <tr>
                                    <th class="px-5 py-4">Item</th>
                                    <th class="px-5 py-4">Qtd.</th>
                                    <th class="px-5 py-4">Status</th>
                                    <th class="px-5 py-4 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-base-300/70">
                                @foreach ($ticket->items as $item)
                                    <tr class="transition hover:bg-primary/5">
                                        <td class="px-5 py-4">
                                            <div class="font-semibold text-neutral">{{ $item->product_name }}</div>
                                            @if ($item->notes)
                                                <div class="text-xs text-base-content/60">{{ $item->notes }}</div>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 font-semibold text-neutral">{{ $item->quantity }}</td>
                                        <td class="px-5 py-4">
                                            <x-status-pill :label="$itemLabels[$item->status] ?? $item->status" :tone="$itemTones[$item->status] ?? 'neutral'" />
                                        </td>
                                        <td class="px-5 py-4 text-right font-semibold text-neutral">{{ $money($item->subtotal) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t border-base-300/80 bg-base-200/60 p-5 text-right text-lg font-bold text-neutral">
                        Total: {{ $money($ticket->total_amount) }}
                    </div>
                </x-card>

                <div class="flex flex-col gap-6">
                    <x-card>
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h2 class="text-base font-semibold text-neutral">Ações rápidas</h2>
                                <p class="text-sm text-base-content/55">Atalhos principais desta comanda.</p>
                            </div>
                            <flux:icon.bars-arrow-down class="size-6 text-primary" />
                        </div>

                        <div class="grid gap-2">
                            @if ($ticket->status === 'aberta')
                                <x-form :action="route('ticket-list.start-preparation', $ticket)" post>
                                    <x-primary-button type="submit" class="min-h-11 w-full gap-2" data-loading-label="Enviando">
                                        <flux:icon.play class="size-4" />
                                        Enviar para preparo
                                    </x-primary-button>
                                </x-form>
                            @endif

                            @if (! in_array($ticket->status, ['paga', 'cancelada'], true))
                                <a href="#pagamento" class="btn btn-primary btn-soft min-h-11 gap-2">
                                    <flux:icon.banknotes class="size-4" />
                                    Ir para pagamento
                                </a>
                            @endif

                            <a href="#adicionar-itens" class="btn btn-ghost min-h-11 gap-2">
                                <flux:icon.plus class="size-4" />
                                Adicionar itens
                            </a>
                        </div>
                    </x-card>

                    <x-card id="pagamento" class="scroll-mt-24">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h2 class="text-base font-semibold text-neutral">Pagamento</h2>
                                <p class="text-sm text-base-content/55">Fechamento financeiro.</p>
                            </div>
                            <flux:icon.banknotes class="size-6 text-secondary" />
                        </div>

                        @if ($ticket->status === 'paga')
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between rounded-md bg-base-200/70 p-3"><span>Forma</span><strong>{{ ucfirst($ticket->payment_method ?? '-') }}</strong></div>
                                <div class="flex justify-between rounded-md bg-base-200/70 p-3"><span>Desconto</span><strong>{{ $money($ticket->discount_amount) }}</strong></div>
                                <div class="flex justify-between rounded-md bg-base-200/70 p-3"><span>Serviço</span><strong>{{ $money($ticket->service_amount) }}</strong></div>
                                <div class="flex justify-between rounded-md bg-success/10 p-3 text-base text-success"><span>Total pago</span><strong>{{ $money($ticket->paid_amount) }}</strong></div>
                            </div>
                        @elseif ($ticket->status === 'cancelada')
                            <x-alert-message tone="warning">Comanda cancelada.</x-alert-message>
                        @else
                            <x-form :action="route('ticket-list.pay', $ticket)" post>
                                <label class="form-control">
                                    <x-input-label value="Forma de pagamento" />
                                    <select name="payment_method" class="select select-bordered min-h-11 w-full bg-base-100">
                                        <option value="">Selecione</option>
                                        <option value="dinheiro">Dinheiro</option>
                                        <option value="pix">Pix</option>
                                        <option value="debito">Débito</option>
                                        <option value="credito">Crédito</option>
                                        <option value="outro">Outro</option>
                                    </select>
                                </label>

                                <div class="grid gap-3 sm:grid-cols-2">
                                    <label class="form-control">
                                        <x-input-label value="Desconto" />
                                        <x-text-input name="discount_amount" type="number" min="0" step="0.01" value="0" class="min-h-11" />
                                    </label>

                                    <label class="form-control">
                                        <x-input-label value="Serviço/acréscimo" />
                                        <x-text-input name="service_amount" type="number" min="0" step="0.01" value="0" class="min-h-11" />
                                    </label>
                                </div>

                                <x-primary-button type="submit" class="min-h-11 gap-2" data-loading-label="Salvando">
                                    <flux:icon.check class="size-4" />
                                    Marcar como paga
                                </x-primary-button>
                            </x-form>
                        @endif
                    </x-card>

                    <x-card>
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h2 class="text-base font-semibold text-neutral">Status da comanda</h2>
                                <p class="text-sm text-base-content/55">Controle do fluxo operacional.</p>
                            </div>
                            <x-status-pill :label="$statusLabels[$ticket->status] ?? $ticket->status" :tone="$statusTones[$ticket->status] ?? 'neutral'" />
                        </div>

                        <x-form :action="route('ticket-list.status.update', $ticket)" post>
                            @method('PATCH')

                            <label class="form-control">
                                <x-input-label value="Status atual" />
                                <select name="status" class="select select-bordered min-h-11 w-full bg-base-100">
                                    @foreach ($statusLabels as $status => $label)
                                        <option value="{{ $status }}" @selected(old('status', $ticket->status) === $status)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>

                            <x-primary-button type="submit" class="min-h-11 gap-2" data-loading-label="Atualizando">
                                <flux:icon.arrow-path class="size-4" />
                                Atualizar status
                            </x-primary-button>
                        </x-form>
                    </x-card>

                    <x-card id="adicionar-itens" class="scroll-mt-24">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h2 class="text-base font-semibold text-neutral">Adicionar itens</h2>
                                <p class="text-sm text-base-content/55">Inclua novos produtos na comanda.</p>
                            </div>
                            <flux:icon.plus class="size-6 text-primary" />
                        </div>

                        @if (in_array($ticket->status, ['fechada', 'paga', 'cancelada'], true))
                            <x-alert-message tone="warning">Esta comanda já foi encerrada.</x-alert-message>
                        @else
                            <x-form :action="route('ticket-list.items.store', $ticket)" post>
                                @for ($index = 0; $index < 4; $index++)
                                    <div class="rounded-lg border border-base-300/80 bg-base-200/70 p-3 sm:p-4">
                                        <div class="grid gap-3">
                                            <label class="grid gap-2">
                                                <x-input-label value="Item" />
                                                <select name="items[{{ $index }}][menu_item_id]" class="select select-bordered min-h-11 w-full bg-base-100" @disabled($menuItems->isEmpty())>
                                                    <option value="">Selecione</option>
                                                    @foreach ($menuItems as $menuItem)
                                                        <option value="{{ $menuItem->id }}" @selected((string) old("items.$index.menu_item_id") === (string) $menuItem->id)>
                                                            {{ $menuItem->name }} - {{ $money($menuItem->price) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </label>

                                            <label class="grid gap-2">
                                                <x-input-label value="Quantidade" />
                                                <input
                                                    name="items[{{ $index }}][quantity]"
                                                    type="number"
                                                    inputmode="numeric"
                                                    min="1"
                                                    step="1"
                                                    value="{{ old("items.$index.quantity", 1) }}"
                                                    placeholder="1"
                                                    class="block min-h-11 w-full rounded-lg border border-base-300 bg-base-100 px-3 py-2 text-base text-base-content shadow-sm"
                                                >
                                            </label>

                                            <label class="grid gap-2">
                                                <x-input-label value="Observações" />
                                                <input
                                                    name="items[{{ $index }}][notes]"
                                                    value="{{ old("items.$index.notes") }}"
                                                    placeholder="Ponto, adicionais, retirada..."
                                                    class="block min-h-11 w-full rounded-lg border border-base-300 bg-base-100 px-3 py-2 text-base text-base-content shadow-sm"
                                                >
                                            </label>
                                        </div>
                                    </div>
                                @endfor

                                <x-primary-button type="submit" class="min-h-11 gap-2" :disabled="$menuItems->isEmpty()" data-loading-label="Adicionando">
                                    <flux:icon.plus class="size-4" />
                                    Adicionar
                                </x-primary-button>
                            </x-form>
                        @endif
                    </x-card>

                    <x-card>
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h2 class="text-base font-semibold text-neutral">Histórico</h2>
                                <p class="text-sm text-base-content/55">Eventos operacionais registrados.</p>
                            </div>
                            <flux:icon.clock class="size-6 text-info" />
                        </div>

                        @if ($ticket->events->isEmpty())
                            <p class="text-sm text-base-content/60">Nenhum evento registrado.</p>
                        @else
                            <div class="space-y-3">
                                @foreach ($ticket->events as $event)
                                    <div class="rounded-lg border border-base-300/80 bg-base-100 p-3 text-sm">
                                        <div class="flex justify-between gap-3">
                                            <strong class="text-neutral">{{ $event->event }}</strong>
                                            <span class="text-xs text-base-content/55">{{ $event->created_at->format('d/m H:i') }}</span>
                                        </div>
                                        <p class="mt-1 text-xs text-base-content/60">{{ $event->user?->name ?? 'Sistema' }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </x-card>
                </div>
            </section>
        </div>
    </div>
</x-layouts::app>
