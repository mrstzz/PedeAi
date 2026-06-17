<x-layouts::app :title="__('Novo item')">
    <div class="min-h-full text-base-content">
        <div class="mx-auto flex w-full max-w-4xl flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8">
            <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="badge badge-secondary badge-outline mb-3">Admin</div>
                    <h1 class="text-2xl font-bold text-neutral sm:text-3xl">Novo item</h1>
                    <p class="mt-2 max-w-2xl text-sm text-base-content/70">
                        Defina nome, valor e disponibilidade do item que aparecera para o atendente.
                    </p>
                </div>

                <x-link-button href="{{ route('menu-items.index') }}" class="min-h-11">
                    Voltar
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

            <x-card>
                <x-form :action="route('menu-items.store')" post>
                    <label class="form-control">
                        <x-input-label value="Nome do item" />
                        <x-text-input name="name" value="{{ old('name') }}" placeholder="Ex: X-salada, refrigerante, porção..." required />
                    </label>

                    <label class="form-control">
                        <x-input-label value="Valor" />
                        <x-text-input name="price" type="number" min="0" step="0.01" value="{{ old('price') }}" placeholder="0,00" required />
                    </label>

                    <label class="form-control">
                        <x-input-label value="Descrição" />
                        <textarea
                            name="description"
                            class="textarea textarea-bordered min-h-24 bg-base-100"
                            placeholder="Detalhes que ajudam o atendente a identificar o item"
                        >{{ old('description') }}</textarea>
                    </label>

                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="hidden" name="active" value="0">
                        <input type="checkbox" name="active" value="1" class="toggle toggle-primary" @checked(old('active', '1') === '1')>
                        <span class="label-text">Disponível para atendentes</span>
                    </label>

                    <div class="flex flex-col-reverse gap-3 border-t border-base-300 pt-4 sm:flex-row sm:justify-end">
                        <x-secondary-button type="reset">Limpar</x-secondary-button>
                        <x-primary-button type="submit">Salvar item</x-primary-button>
                    </div>
                </x-form>
            </x-card>
        </div>
    </div>
</x-layouts::app>
