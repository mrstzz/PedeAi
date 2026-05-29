<x-layouts::app :title="__('Nova comanda')">
    <div class="min-h-full bg-base-200 text-base-content">
        <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8">
            <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="badge badge-secondary badge-outline mb-3">Cadastro</div>
                    <h1 class="text-2xl font-bold text-neutral sm:text-3xl">Nova comanda</h1>
                    <p class="mt-2 max-w-2xl text-sm text-base-content/70">
                        Informe os dados do atendimento e os itens consumidos para abrir um novo ticket.
                    </p>
                </div>

                <x-link-button href="{{ route('ticket-list.index') }}" class="min-h-11">
                    Voltar para comandas
                </x-link-button>
            </section>

            @if ($errors->any())
                <div class="alert alert-error">
                    <div>
                        <h2 class="font-semibold">Revise os dados informados</h2>
                        <p class="text-sm">{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            @if ($menuItems->isEmpty())
                <div class="alert alert-warning">
                    <div>
                        <h2 class="font-semibold">Nenhum item disponivel</h2>
                        <p class="text-sm">
                            Um usuario admin precisa cadastrar itens ativos antes do atendente criar comandas.
                        </p>
                    </div>
                </div>
            @endif

            <x-card>
                <x-form :action="route('ticket-list.store')" post>
                    <section class="grid gap-4 lg:grid-cols-4">
                        <label class="form-control">
                            <x-input-label value="Cliente" />
                            <x-text-input
                                name="customer_name"
                                value="{{ old('customer_name') }}"
                                placeholder="Nome do cliente"
                            />
                        </label>

                        <label class="form-control">
                            <x-input-label value="Mesa / Balcao" />
                            <x-text-input
                                name="table_number"
                                value="{{ old('table_number') }}"
                                placeholder="Ex: 12, balcao ou delivery"
                            />
                        </label>

                        <label class="form-control">
                            <x-input-label value="Status" />
                            <select name="status" class="select select-bordered w-full bg-base-100">
                                <option value="aberta" @selected(old('status', 'aberta') === 'aberta')>Aberta</option>
                                <option value="em_andamento" @selected(old('status') === 'em_andamento')>Em andamento</option>
                                <option value="fechada" @selected(old('status') === 'fechada')>Fechada</option>
                                <option value="paga" @selected(old('status') === 'paga')>Paga</option>
                                <option value="cancelada" @selected(old('status') === 'cancelada')>Cancelada</option>
                            </select>
                        </label>

                        <label class="form-control">
                            <x-input-label value="Prioridade" />
                            <select name="priority" class="select select-bordered w-full bg-base-100">
                                <option value="normal" @selected(old('priority', 'normal') === 'normal')>Normal</option>
                                <option value="alta" @selected(old('priority') === 'alta')>Alta</option>
                            </select>
                        </label>
                    </section>

                    <label class="form-control">
                        <x-input-label value="Observacoes da comanda" />
                         <x-text-input
                            id="notes"
                            type="text"
                            class="textarea textarea-bordered min-h-24 bg-base-100"
                            name="notes"
                            placeholder="Ex: sem cebola, aniversariante, pedido prioritario..."
                            :value="old('notes')"/>
                    </label>

                    @php
                        $itemRows = old('items', [
                            ['menu_item_id' => null, 'quantity' => 1, 'notes' => null],
                        ]);
                    @endphp

                    <section class="space-y-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-neutral">Itens da comanda</h2>
                                <p class="text-sm text-base-content/65">Adicione os itens consumidos. O total sera calculado automaticamente ao salvar.</p>
                            </div>

                            <button
                                type="button"
                                class="btn btn-primary btn-soft"
                                data-add-ticket-item
                                @disabled($menuItems->isEmpty())
                            >
                                Adicionar item
                            </button>
                        </div>

                        <div class="grid gap-4" data-ticket-items>
                            @foreach ($itemRows as $index => $itemRow)
                                <div class="rounded-lg border border-base-300 bg-base-200 p-4" data-ticket-item-row>
                                    <div class="mb-3 flex items-center justify-between">
                                        <span class="badge badge-primary badge-outline" data-ticket-item-label>Item {{ $index + 1 }}</span>

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
                                            <select name="items[{{ $index }}][menu_item_id]" class="select select-bordered w-full bg-base-100" @disabled($menuItems->isEmpty())>
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
                                                class="input input-bordered w-full bg-base-100"
                                            />
                                        </label>
                                    </div>

                                    <label class="form-control mt-4">
                                        <x-input-label value="Observacoes do item" />
                                        <x-text-input
                                            name="items[{{ $index }}][notes]"
                                            value="{{ old('items.' . $index . '.notes', $itemRow['notes'] ?? '') }}"
                                            placeholder="Ponto da carne, adicionais, retirada..."
                                        />
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <div class="flex flex-col-reverse gap-3 border-t border-base-300 pt-4 sm:flex-row sm:justify-end">
                        <x-secondary-button type="reset">Limpar</x-secondary-button>
                        <x-primary-button type="submit" :disabled="$menuItems->isEmpty()">Salvar comanda</x-primary-button>
                    </div>
                </x-form>
            </x-card>
        </div>
    </div>

    <template id="ticket-item-template">
        <div class="rounded-lg border border-base-300 bg-base-200 p-4" data-ticket-item-row>
            <div class="mb-3 flex items-center justify-between">
                <span class="badge badge-primary badge-outline" data-ticket-item-label>Item __NUMBER__</span>

                <button type="button" class="btn btn-ghost btn-xs text-error" data-remove-ticket-item>
                    Remover
                </button>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-[minmax(0,1fr)_9rem]">
                <label class="form-control min-w-0">
                    <x-input-label value="Item do cardapio" />
                    <select name="items[__INDEX__][menu_item_id]" class="select select-bordered w-full bg-base-100" @disabled($menuItems->isEmpty())>
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
                        class="input input-bordered w-full bg-base-100"
                    />
                </label>
            </div>

            <label class="form-control mt-4">
                <x-input-label value="Observacoes do item" />
                <x-text-input
                    name="items[__INDEX__][notes]"
                    placeholder="Ponto da carne, adicionais, retirada..."
                />
            </label>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const list = document.querySelector('[data-ticket-items]');
            const addButton = document.querySelector('[data-add-ticket-item]');
            const template = document.querySelector('#ticket-item-template');

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

            refreshRows();
        });
    </script>
</x-layouts::app>
