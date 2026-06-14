<x-layouts::app :title="__('Editar item')">
    <div class="min-h-full text-base-content">
        <div class="mx-auto flex w-full max-w-[100rem] flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8 2xl:px-10">
            <x-page-header
                eyebrow="Admin"
                title="Editar item"
                description="Atualize nome, valor, descrição e disponibilidade do item."
                icon="book-open"
            >
                <x-link-button href="{{ route('menu-items.index') }}" class="min-h-11 gap-2 whitespace-nowrap px-4">
                    <flux:icon.arrow-left class="size-4" />
                    Voltar
                </x-link-button>
            </x-page-header>

            @if ($errors->any())
                <x-alert-message tone="error">
                    {{ $errors->first() }}
                </x-alert-message>
            @endif

            <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]">
                <x-card>
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="truncate text-lg font-semibold text-neutral">{{ $item->name }}</h2>
                            <p class="text-sm text-base-content/55">Cadastro do item do cardápio.</p>
                        </div>

                        <x-status-pill :label="$item->active ? 'Ativo' : 'Inativo'" :tone="$item->active ? 'success' : 'neutral'" />
                    </div>

                    <x-form :action="route('menu-items.update', $item)" post>
                        @method('PATCH')

                        <section class="grid gap-4 sm:grid-cols-2">
                            <label class="form-control">
                                <x-input-label value="Nome do item" />
                                <x-text-input name="name" value="{{ old('name', $item->name) }}" class="min-h-11" required />
                            </label>

                            <label class="form-control">
                                <x-input-label value="Valor" />
                                <x-text-input name="price" type="number" min="0" step="0.01" value="{{ old('price', $item->price) }}" class="min-h-11" required />
                            </label>
                        </section>

                        <label class="form-control">
                            <x-input-label value="Descrição" />
                            <textarea name="description" class="textarea textarea-bordered min-h-28 w-full bg-base-100">{{ old('description', $item->description) }}</textarea>
                        </label>

                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-base-300/80 bg-base-200/70 p-4">
                            <input type="hidden" name="active" value="0">
                            <input type="checkbox" name="active" value="1" class="toggle toggle-primary" @checked(old('active', $item->active ? '1' : '0') === '1')>
                            <span class="text-sm font-medium text-neutral">Disponível para atendentes</span>
                        </label>

                        <div class="flex flex-col-reverse gap-3 border-t border-base-300/80 pt-4 sm:flex-row sm:justify-end">
                            <x-secondary-button type="reset" class="min-h-11 gap-2">
                                <flux:icon.arrow-path class="size-4" />
                                Limpar
                            </x-secondary-button>
                            <x-primary-button type="submit" class="min-h-11 gap-2">
                                <flux:icon.check class="size-4" />
                                Salvar alterações
                            </x-primary-button>
                        </div>
                    </x-form>
                </x-card>

                <aside class="flex flex-col gap-4">
                    <x-metric-card
                        label="Valor atual"
                        value="{{ 'R$ '.number_format((float) $item->price, 2, ',', '.') }}"
                        description="Preço usado nas novas comandas."
                        icon="banknotes"
                        accent="text-secondary bg-secondary/10 ring-secondary/15"
                    />
                    <x-card>
                        <div class="flex items-start gap-3">
                            <div class="grid size-10 shrink-0 place-items-center rounded-lg bg-primary/10 text-primary ring-1 ring-primary/15">
                                <flux:icon.information-circle class="size-5" />
                            </div>
                            <p class="text-sm leading-6 text-base-content/65">
                                Desativar um item remove ele das novas comandas, mas preserva registros já criados.
                            </p>
                        </div>
                    </x-card>
                </aside>
            </section>
        </div>
    </div>
</x-layouts::app>
