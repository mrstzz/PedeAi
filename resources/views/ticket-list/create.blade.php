@php
    $itemRows = old('items', [
        ['menu_item_id' => null, 'quantity' => 1, 'notes' => null],
    ]);

    $setupMetrics = [
        [
            'label' => 'Itens ativos',
            'value' => $menuItems->count(),
            'description' => 'Disponiveis para consumo',
            'icon' => 'book-open',
            'accent' => 'text-primary bg-primary/10 ring-primary/15',
        ],
        [
            'label' => 'Mesas livres',
            'value' => $availableTables->count(),
            'description' => 'Podem receber comandas',
            'icon' => 'table-cells',
            'accent' => 'text-success bg-success/10 ring-success/15',
        ],
        [
            'label' => 'Reservas ativas',
            'value' => $activeReservations->count(),
            'description' => 'Prontas para vinculo',
            'icon' => 'calendar-days',
            'accent' => 'text-secondary bg-secondary/10 ring-secondary/15',
        ],
    ];
@endphp

<x-layouts::app :title="__('Nova comanda')">
    <div class="min-h-full text-base-content">
        <div class="mx-auto flex w-full max-w-[100rem] flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8 2xl:px-10">
            <section class="rounded-lg border border-base-300/80 bg-base-100/90 p-5 shadow-sm backdrop-blur sm:p-6">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div class="min-w-0">
                        <div class="mb-3 inline-flex items-center gap-2 rounded-md border border-secondary/25 bg-secondary/10 px-3 py-1 text-xs font-semibold uppercase tracking-normal text-secondary">
                            <span class="size-1.5 rounded-full bg-secondary"></span>
                            Cadastro
                        </div>
                        <h1 class="text-3xl font-bold tracking-normal text-neutral sm:text-4xl">Nova comanda</h1>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-base-content/65">
                            Informe os dados do atendimento e os itens consumidos para abrir um novo ticket.
                        </p>
                    </div>

                    <x-link-button href="{{ route('ticket-list.index') }}" class="min-h-11 gap-2 whitespace-nowrap px-4">
                        <flux:icon.arrow-left class="size-4" />
                        Voltar para comandas
                    </x-link-button>
                </div>
            </section>

            <section class="grid gap-4 md:grid-cols-3">
                @foreach ($setupMetrics as $metric)
                    <article class="rounded-lg border border-base-300/80 bg-base-100 p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-base-content/60">{{ $metric['label'] }}</p>
                                <p class="mt-2 text-4xl font-bold tracking-normal text-neutral">{{ $metric['value'] }}</p>
                            </div>

                            <div class="grid size-11 place-items-center rounded-lg ring-1 {{ $metric['accent'] }}">
                                <flux:icon :name="$metric['icon']" class="size-5" />
                            </div>
                        </div>

                        <p class="mt-4 border-t border-base-300/70 pt-3 text-sm text-base-content/60">{{ $metric['description'] }}</p>
                    </article>
                @endforeach
            </section>

            @if ($errors->any())
                <div class="alert alert-error rounded-lg border border-error/20 bg-error/10 text-error">
                    <flux:icon.exclamation-triangle class="size-5" />
                    <div>
                        <h2 class="font-semibold">Revise os dados informados</h2>
                        <p class="text-sm">{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            @if ($menuItems->isEmpty())
                <div class="alert alert-warning rounded-lg border border-warning/30 bg-warning/10 text-warning">
                    <flux:icon.exclamation-triangle class="size-5" />
                    <div>
                        <h2 class="font-semibold">Nenhum item disponivel</h2>
                        <p class="text-sm">
                            Um usuario admin precisa cadastrar itens ativos antes do atendente criar comandas.
                        </p>
                    </div>
                </div>
            @endif

            @if ($availableTables->isEmpty() && $activeReservations->isEmpty())
                <div class="alert alert-warning rounded-lg border border-warning/30 bg-warning/10 text-warning">
                    <flux:icon.exclamation-triangle class="size-5" />
                    <div>
                        <h2 class="font-semibold">Nenhuma mesa livre no momento</h2>
                        <p class="text-sm">
                            Libere uma mesa, conclua uma comanda aberta ou selecione uma reserva confirmada dentro do horario.
                        </p>
                    </div>
                </div>
            @endif

            <x-form :action="route('ticket-list.store')" post class="gap-6">
                <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]">
                    <div class="flex flex-col gap-6">
                        <x-card>
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <h2 class="text-lg font-semibold text-neutral">Dados do atendimento</h2>
                                    <p class="text-sm text-base-content/55">Cliente, mesa, reserva e prioridade da comanda.</p>
                                </div>

                                <div class="grid size-10 place-items-center rounded-lg bg-primary/10 text-primary ring-1 ring-primary/15">
                                    <flux:icon.clipboard-document-list class="size-5" />
                                </div>
                            </div>

                            <section class="grid gap-4 lg:grid-cols-2 2xl:grid-cols-4">
                                <label class="form-control">
                                    <x-input-label value="Cliente" />
                                    <x-text-input
                                        name="customer_name"
                                        value="{{ old('customer_name') }}"
                                        placeholder="Nome do cliente"
                                        class="min-h-11"
                                    />
                                </label>

                                <label class="form-control">
                                    <x-input-label value="Reserva confirmada" />
                                    <select name="reservation_id" class="select select-bordered min-h-11 w-full bg-base-100" data-reservation-select>
                                        <option value="">Sem reserva</option>
                                        @foreach ($activeReservations as $reservation)
                                            <option value="{{ $reservation->id }}" @selected((string) old('reservation_id') === (string) $reservation->id)>
                                                {{ $reservation->customer_name }} - Mesa {{ $reservation->restaurantTable?->identifier }} - {{ $reservation->reserved_at->timezone('America/Sao_Paulo')->format('H:i') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </label>

                                <label class="form-control">
                                    <x-input-label value="Mesa livre" />
                                    <select name="restaurant_table_id" class="select select-bordered min-h-11 w-full bg-base-100" data-table-select @disabled($availableTables->isEmpty())>
                                        <option value="">Selecione uma mesa</option>
                                        @foreach ($availableTables as $table)
                                            <option value="{{ $table->id }}" @selected((string) old('restaurant_table_id') === (string) $table->id)>
                                                Mesa {{ $table->identifier }} - {{ $table->capacity }} lugares
                                            </option>
                                        @endforeach
                                    </select>
                                </label>

                                <label class="form-control">
                                    <x-input-label value="Prioridade" />
                                    <select name="priority" class="select select-bordered min-h-11 w-full bg-base-100">
                                        <option value="normal" @selected(old('priority', 'normal') === 'normal')>Normal</option>
                                        <option value="alta" @selected(old('priority') === 'alta')>Alta</option>
                                    </select>
                                </label>
                            </section>

                            <label class="form-control">
                                <x-input-label value="Observacoes da comanda" />
                                <textarea
                                    id="notes"
                                    name="notes"
                                    class="textarea textarea-bordered min-h-28 w-full bg-base-100"
                                    placeholder="Ex: sem cebola, aniversariante, pedido prioritario..."
                                >{{ old('notes') }}</textarea>
                            </label>
                        </x-card>

                        <x-card>
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h2 class="text-lg font-semibold text-neutral">Itens da comanda</h2>
                                    <p class="text-sm text-base-content/55">Adicione os itens consumidos. O total sera calculado automaticamente ao salvar.</p>
                                </div>

                                <button
                                    type="button"
                                    class="btn btn-primary btn-soft min-h-10 gap-2"
                                    data-add-ticket-item
                                    @disabled($menuItems->isEmpty())
                                >
                                    <flux:icon.plus class="size-4" />
                                    Adicionar item
                                </button>
                            </div>

                            <div class="grid gap-4" data-ticket-items>
                                @foreach ($itemRows as $index => $itemRow)
                                    <div class="rounded-lg border border-base-300/80 bg-base-200/70 p-4 shadow-sm" data-ticket-item-row>
                                        <div class="mb-4 flex items-center justify-between gap-3">
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary ring-1 ring-primary/20" data-ticket-item-label>
                                                Item {{ $index + 1 }}
                                            </span>

                                            <button
                                                type="button"
                                                class="btn btn-ghost btn-xs text-error"
                                                data-remove-ticket-item
                                                @if ($loop->first && count($itemRows) === 1) hidden @endif
                                            >
                                                Remover
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-1 gap-4 md:grid-cols-[minmax(0,1fr)_9rem]">
                                            <label class="form-control min-w-0">
                                                <x-input-label value="Item do cardapio" />
                                                <select name="items[{{ $index }}][menu_item_id]" class="select select-bordered min-h-11 w-full bg-base-100" @disabled($menuItems->isEmpty())>
                                                    <option value="">Selecione um item</option>
                                                    @foreach ($menuItems as $menuItem)
                                                        <option value="{{ $menuItem->id }}" @selected((string) old('items.' . $index . '.menu_item_id', $itemRow['menu_item_id'] ?? '') === (string) $menuItem->id)>
                                                            {{ $menuItem->name }} - R$ {{ number_format((float) $menuItem->price, 2, ',', '.') }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </label>

                                            <label class="form-control w-full min-w-28">
                                                <x-input-label value="Qtd." />
                                                <input
                                                    name="items[{{ $index }}][quantity]"
                                                    type="number"
                                                    inputmode="numeric"
                                                    min="1"
                                                    step="1"
                                                    value="{{ old('items.' . $index . '.quantity', $itemRow['quantity'] ?? 1) }}"
                                                    placeholder="1"
                                                    class="input input-bordered min-h-11 w-full bg-base-100"
                                                />
                                            </label>
                                        </div>

                                        <label class="form-control mt-4">
                                            <x-input-label value="Observacoes do item" />
                                            <x-text-input
                                                name="items[{{ $index }}][notes]"
                                                value="{{ old('items.' . $index . '.notes', $itemRow['notes'] ?? '') }}"
                                                placeholder="Ponto da carne, adicionais, retirada..."
                                                class="min-h-11"
                                            />
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </x-card>
                    </div>

                    <aside class="flex flex-col gap-4">
                        <x-card>
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <h2 class="text-base font-semibold text-neutral">Resumo da abertura</h2>
                                    <p class="text-sm text-base-content/55">Confira antes de salvar.</p>
                                </div>

                                <div class="grid size-10 place-items-center rounded-lg bg-secondary/10 text-secondary ring-1 ring-secondary/15">
                                    <flux:icon.information-circle class="size-5" />
                                </div>
                            </div>

                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between gap-3 rounded-md bg-base-200/70 p-3">
                                    <span class="text-base-content/60">Itens iniciais</span>
                                    <strong class="text-neutral">{{ count($itemRows) }}</strong>
                                </div>
                                <div class="flex justify-between gap-3 rounded-md bg-base-200/70 p-3">
                                    <span class="text-base-content/60">Mesas livres</span>
                                    <strong class="text-neutral">{{ $availableTables->count() }}</strong>
                                </div>
                                <div class="flex justify-between gap-3 rounded-md bg-base-200/70 p-3">
                                    <span class="text-base-content/60">Reservas</span>
                                    <strong class="text-neutral">{{ $activeReservations->count() }}</strong>
                                </div>
                            </div>
                        </x-card>

                        <x-card>
                            <div class="flex items-start gap-3">
                                <div class="grid size-10 shrink-0 place-items-center rounded-lg bg-primary/10 text-primary ring-1 ring-primary/15">
                                    <flux:icon.light-bulb class="size-5" />
                                </div>
                                <p class="text-sm leading-6 text-base-content/65">
                                    Se uma reserva for selecionada, a mesa livre fica desabilitada para evitar vinculo duplicado.
                                </p>
                            </div>
                        </x-card>
                    </aside>
                </section>

                <div class="flex flex-col-reverse gap-3 rounded-lg border border-base-300/80 bg-base-100 p-4 shadow-sm sm:flex-row sm:justify-end">
                    <x-secondary-button type="reset" class="min-h-11 gap-2">
                        <flux:icon.arrow-path class="size-4" />
                        Limpar
                    </x-secondary-button>
                    <x-primary-button type="submit" class="min-h-11 gap-2" :disabled="$menuItems->isEmpty() || ($availableTables->isEmpty() && $activeReservations->isEmpty())">
                        <flux:icon.check class="size-4" />
                        Salvar comanda
                    </x-primary-button>
                </div>
            </x-form>
        </div>
    </div>

    <template id="ticket-item-template">
        <div class="rounded-lg border border-base-300/80 bg-base-200/70 p-4 shadow-sm" data-ticket-item-row>
            <div class="mb-4 flex items-center justify-between gap-3">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary ring-1 ring-primary/20" data-ticket-item-label>Item __NUMBER__</span>

                <button type="button" class="btn btn-ghost btn-xs text-error" data-remove-ticket-item>
                    Remover
                </button>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-[minmax(0,1fr)_9rem]">
                <label class="form-control min-w-0">
                    <x-input-label value="Item do cardapio" />
                    <select name="items[__INDEX__][menu_item_id]" class="select select-bordered min-h-11 w-full bg-base-100" @disabled($menuItems->isEmpty())>
                        <option value="">Selecione um item</option>
                        @foreach ($menuItems as $menuItem)
                            <option value="{{ $menuItem->id }}">
                                {{ $menuItem->name }} - R$ {{ number_format((float) $menuItem->price, 2, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="form-control w-full min-w-28">
                    <x-input-label value="Qtd." />
                    <input
                        name="items[__INDEX__][quantity]"
                        type="number"
                        inputmode="numeric"
                        min="1"
                        step="1"
                        value="1"
                        placeholder="1"
                        class="input input-bordered min-h-11 w-full bg-base-100"
                    />
                </label>
            </div>

            <label class="form-control mt-4">
                <x-input-label value="Observacoes do item" />
                <x-text-input
                    name="items[__INDEX__][notes]"
                    placeholder="Ponto da carne, adicionais, retirada..."
                    class="min-h-11"
                />
            </label>
        </div>
    </template>

    <script>
        (() => {
            const initTicketCreate = () => {
                const list = document.querySelector('[data-ticket-items]');
                const addButton = document.querySelector('[data-add-ticket-item]');
                const template = document.querySelector('#ticket-item-template');
                const reservationSelect = document.querySelector('[data-reservation-select]');
                const tableSelect = document.querySelector('[data-table-select]');

                if (!list || !addButton || !template) {
                    return;
                }

                const refreshRows = () => {
                    const rows = [...list.querySelectorAll('[data-ticket-item-row]')];

                    rows.forEach((row, index) => {
                        row.querySelector('[data-ticket-item-label]').textContent = `Item ${index + 1}`;
                        row.querySelectorAll('[name]').forEach((field) => {
                            field.name = field.name.replace(/items\[\d+]/, `items[${index}]`);
                        });

                        const removeButton = row.querySelector('[data-remove-ticket-item]');
                        removeButton.hidden = rows.length === 1;
                    });
                };

                const syncReservationAndTable = () => {
                    if (!reservationSelect || !tableSelect) {
                        return;
                    }

                    const hasReservation = reservationSelect.value !== '';

                    if (hasReservation) {
                        tableSelect.value = '';
                    }

                    tableSelect.disabled = hasReservation || tableSelect.dataset.empty === 'true';
                    tableSelect.classList.toggle('opacity-60', hasReservation);
                };

                if (!list.dataset.ticketItemsReady) {
                    addButton.addEventListener('click', () => {
                        const index = list.querySelectorAll('[data-ticket-item-row]').length;
                        const html = template.innerHTML
                            .replaceAll('__INDEX__', index)
                            .replaceAll('__NUMBER__', index + 1);

                        list.insertAdjacentHTML('beforeend', html);
                        refreshRows();
                    });

                    list.addEventListener('click', (event) => {
                        const removeButton = event.target.closest('[data-remove-ticket-item]');

                        if (!removeButton) {
                            return;
                        }

                        removeButton.closest('[data-ticket-item-row]').remove();
                        refreshRows();
                    });

                    list.dataset.ticketItemsReady = 'true';
                }

                if (reservationSelect && !reservationSelect.dataset.reservationReady) {
                    reservationSelect.addEventListener('change', syncReservationAndTable);
                    reservationSelect.dataset.reservationReady = 'true';
                }

                if (tableSelect && !tableSelect.dataset.empty) {
                    tableSelect.dataset.empty = tableSelect.disabled ? 'true' : 'false';
                }

                refreshRows();
                syncReservationAndTable();
            };

            initTicketCreate();

            if (!window.__pedeaiTicketCreateBound) {
                document.addEventListener('DOMContentLoaded', initTicketCreate);
                document.addEventListener('livewire:navigated', initTicketCreate);
                window.__pedeaiTicketCreateBound = true;
            }
        })();
    </script>
</x-layouts::app>
