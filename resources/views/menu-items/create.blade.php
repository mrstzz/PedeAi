<x-layouts::app :title="__('Novo item')">
    <div class="min-h-full text-base-content">
        <div class="mx-auto flex w-full max-w-[100rem] flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8 2xl:px-10">
            <x-page-header
                eyebrow="Admin"
                title="Novo item"
                description="Defina nome, valor e disponibilidade do item que aparecerá para o atendente."
                icon="book-open"
            >
                <x-link-button href="{{ route('menu-items.index') }}" class="min-h-11 gap-2 whitespace-nowrap px-4">
                    <flux:icon.arrow-left class="size-4" />
                    Voltar
                </x-link-button>
            </x-page-header>

            @if ($errors->any())
                <x-alert-message tone="error" title="Revise os dados informados">
                    {{ $errors->first() }}
                </x-alert-message>
            @endif

            <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]">
                <x-card>
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-neutral">Dados do item</h2>
                            <p class="text-sm text-base-content/55">Informações usadas na abertura de comandas.</p>
                        </div>

                        <flux:icon.book-open class="size-6 text-primary" />
                    </div>

                    <x-form :action="route('menu-items.store')" post>
                        <section class="grid gap-4 sm:grid-cols-2">
                            <label class="form-control">
                                <x-input-label value="Nome do item" />
                                <x-text-input name="name" value="{{ old('name') }}" placeholder="Ex: X-salada, refrigerante, porção..." class="min-h-11" required />
                            </label>

                            <label class="form-control">
                                <x-input-label value="Valor" />
                                <x-text-input name="price" type="number" min="0" step="0.01" value="{{ old('price') }}" placeholder="0,00" class="min-h-11" required />
                            </label>
                        </section>

                        <label class="form-control">
                            <x-input-label value="Descrição" />
                            <textarea
                                name="description"
                                class="textarea textarea-bordered min-h-28 w-full bg-base-100"
                                placeholder="Detalhes que ajudam o atendente a identificar o item"
                            >{{ old('description') }}</textarea>
                        </label>

                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-base-300/80 bg-base-200/70 p-4">
                            <input type="hidden" name="active" value="0">
                            <input type="checkbox" name="active" value="1" class="toggle toggle-primary" @checked(old('active', '1') === '1')>
                            <span class="text-sm font-medium text-neutral">Disponível para atendentes</span>
                        </label>

                        <div class="flex flex-col-reverse gap-3 border-t border-base-300/80 pt-4 sm:flex-row sm:justify-end">
                            <x-secondary-button type="reset" class="min-h-11 gap-2">
                                <flux:icon.arrow-path class="size-4" />
                                Limpar
                            </x-secondary-button>
                            <x-primary-button type="submit" class="min-h-11 gap-2">
                                <flux:icon.check class="size-4" />
                                Salvar item
                            </x-primary-button>
                        </div>
                    </x-form>
                </x-card>

                <aside class="flex flex-col gap-4">
                    <x-metric-card
                        label="Status inicial"
                        value="Ativo"
                        description="Disponível para novas comandas."
                        icon="check-circle"
                        accent="text-success bg-success/10 ring-success/15"
                    />
                    <x-card>
                        <div class="flex items-start gap-3">
                            <flux:icon.light-bulb class="size-6 shrink-0 text-secondary" />
                            <p class="text-sm leading-6 text-base-content/65">
                                Prefira nomes curtos e descrições objetivas. Isso deixa a abertura da comanda mais rápida no celular.
                            </p>
                        </div>
                    </x-card>
                </aside>
            </section>
        </div>
    </div>
</x-layouts::app>
