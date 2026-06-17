@php
    $money = fn ($value) => 'R$ '.number_format((float) $value, 2, ',', '.');
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

<x-layouts::app :title="__('Detalhes da comanda')">
    <div class="min-h-full text-base-content">
        <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8">
            <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="badge {{ $ticket->priority === 'alta' ? 'badge-error' : 'badge-primary' }} badge-outline mb-3">
                        Prioridade {{ $ticket->priority === 'alta' ? 'alta' : 'normal' }}
                    </div>
                    <h1 class="text-2xl font-bold text-neutral sm:text-3xl">Comanda #{{ $ticket->id }}</h1>
                    <p class="mt-2 text-sm text-base-content/70">
                        {{ $ticket->display_name }}{{ $ticket->restaurantTable ? ' - Mesa '.$ticket->restaurantTable->identifier : ($ticket->table_number ? ' - Mesa '.$ticket->table_number : '') }}
                    </p>
                    @if ($ticket->reservation)
                        <p class="mt-1 text-sm text-base-content/60">
                            Reserva #{{ $ticket->reservation->id }} - {{ $ticket->reservation->reserved_at->timezone('America/Sao_Paulo')->format('d/m/Y H:i') }}
                        </p>
                    @endif
                    <div class="mt-3 badge {{ $statusBadges[$ticket->status] ?? 'badge-neutral' }}">
                        {{ $statusLabels[$ticket->status] ?? $ticket->status }}
                    </div>
                </div>

                <x-link-button href="{{ route('ticket-list.index') }}" class="min-h-11">Voltar</x-link-button>
            </section>

            @if ($errors->any())
                <div class="alert alert-error">
                    <div>
                        <h2 class="font-semibold">Revise os dados informados</h2>
                        <p class="text-sm">{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_24rem]">
                <x-card title="Itens da comanda" bodyClass="p-0">
                    <div class="grid gap-3 p-4 md:hidden">
                        @foreach ($ticket->items as $item)
                            <div class="rounded-lg border border-base-300 bg-base-100 p-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-neutral">{{ $item->product_name }}</p>
                                        <p class="text-sm text-base-content/60">Qtd. {{ $item->quantity }}</p>
                                    </div>
                                    <span class="badge shrink-0 {{ $itemBadges[$item->status] ?? 'badge-neutral' }}">
                                        {{ $itemLabels[$item->status] ?? $item->status }}
                                    </span>
                                </div>
                                @if ($item->notes)
                                    <p class="mt-2 text-xs text-base-content/60">{{ $item->notes }}</p>
                                @endif
                                <p class="mt-2 text-right font-semibold">{{ $money($item->subtotal) }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="hidden overflow-x-auto md:block">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Qtd.</th>
                                    <th>Status</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ticket->items as $item)
                                    <tr>
                                        <td>
                                            <div class="font-semibold text-neutral">{{ $item->product_name }}</div>
                                            @if ($item->notes)
                                                <div class="text-xs text-base-content/60">{{ $item->notes }}</div>
                                            @endif
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>
                                            <span class="badge {{ $itemBadges[$item->status] ?? 'badge-neutral' }}">
                                                {{ $itemLabels[$item->status] ?? $item->status }}
                                            </span>
                                        </td>
                                        <td>{{ $money($item->subtotal) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="border-t border-base-300 p-4 text-right text-lg font-bold">
                        Total: {{ $money($ticket->total_amount) }}
                    </div>
                </x-card>

                <div class="flex flex-col gap-6">
                    <x-card title="Pagamento">
                        @if ($ticket->status === 'paga')
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between"><span>Forma</span><strong>{{ ucfirst($ticket->payment_method ?? '-') }}</strong></div>
                                <div class="flex justify-between"><span>Desconto</span><strong>{{ $money($ticket->discount_amount) }}</strong></div>
                                <div class="flex justify-between"><span>Servico</span><strong>{{ $money($ticket->service_amount) }}</strong></div>
                                <div class="flex justify-between text-base"><span>Total pago</span><strong>{{ $money($ticket->paid_amount) }}</strong></div>
                            </div>
                        @elseif ($ticket->status === 'cancelada')
                            <div class="alert alert-warning">Comanda cancelada.</div>
                        @else
                            <x-form :action="route('ticket-list.pay', $ticket)" post>
                                <label class="form-control">
                                    <x-input-label value="Forma de pagamento" />
                                    <select name="payment_method" class="select select-bordered w-full bg-base-100">
                                        <option value="">Selecione</option>
                                        <option value="dinheiro">Dinheiro</option>
                                        <option value="pix">Pix</option>
                                        <option value="debito">Debito</option>
                                        <option value="credito">Credito</option>
                                        <option value="outro">Outro</option>
                                    </select>
                                </label>

                                <div class="grid gap-3 sm:grid-cols-2">
                                    <label class="form-control">
                                        <x-input-label value="Desconto" />
                                        <x-text-input name="discount_amount" type="number" min="0" step="0.01" value="0" />
                                    </label>

                                    <label class="form-control">
                                        <x-input-label value="Servico/acrescimo" />
                                        <x-text-input name="service_amount" type="number" min="0" step="0.01" value="0" />
                                    </label>
                                </div>

                                <x-primary-button type="submit">Marcar como paga</x-primary-button>
                            </x-form>
                        @endif
                    </x-card>

                    <x-card title="Status da comanda">
                        <x-form :action="route('ticket-list.status.update', $ticket)" post>
                            @method('PATCH')

                            <label class="form-control">
                                <x-input-label value="Status atual" />
                                <select name="status" class="select select-bordered w-full bg-base-100">
                                    @foreach ($statusLabels as $status => $label)
                                        <option value="{{ $status }}" @selected(old('status', $ticket->status) === $status)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>

                            <x-primary-button type="submit">Atualizar status</x-primary-button>
                        </x-form>
                    </x-card>

                    <x-card title="Adicionar itens">
                        @if (in_array($ticket->status, ['fechada', 'paga', 'cancelada'], true))
                            <div class="alert alert-warning">Esta comanda ja foi encerrada.</div>
                        @else
                        <x-form :action="route('ticket-list.items.store', $ticket)" post>
                            @for ($index = 0; $index < 4; $index++)
                                <div class="rounded-lg border border-base-300 bg-base-200 p-4">
                                    <div class="grid gap-3">
                                        <label class="form-control">
                                            <x-input-label value="Item" />
                                            <select name="items[{{ $index }}][menu_item_id]" class="select select-bordered w-full bg-base-100" @disabled($menuItems->isEmpty())>
                                                <option value="">Selecione</option>
                                                @foreach ($menuItems as $menuItem)
                                                    <option value="{{ $menuItem->id }}" @selected((string) old("items.$index.menu_item_id") === (string) $menuItem->id)>
                                                        {{ $menuItem->name }} - {{ $money($menuItem->price) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </label>

                                        <label class="form-control">
                                            <x-input-label value="Quantidade" />
                                            <x-text-input name="items[{{ $index }}][quantity]" type="number" inputmode="numeric" min="1" step="1" value="{{ old("items.$index.quantity") }}" placeholder="1" />
                                        </label>

                                        <label class="form-control">
                                            <x-input-label value="Observações" />
                                            <x-text-input name="items[{{ $index }}][notes]" value="{{ old("items.$index.notes") }}" />
                                        </label>
                                    </div>
                                </div>
                            @endfor

                            <x-primary-button type="submit" :disabled="$menuItems->isEmpty()">Adicionar</x-primary-button>
                        </x-form>
                        @endif
                    </x-card>

                    <x-card title="Historico">
                        @if ($ticket->events->isEmpty())
                            <p class="text-sm text-base-content/60">Nenhum evento registrado.</p>
                        @else
                            <div class="space-y-3">
                                @foreach ($ticket->events as $event)
                                    <div class="rounded-lg border border-base-300 bg-base-100 p-3 text-sm">
                                        <div class="flex justify-between gap-3">
                                            <strong>{{ $event->event }}</strong>
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
