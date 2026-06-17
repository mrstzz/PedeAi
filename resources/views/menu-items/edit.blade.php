<x-layouts::app :title="__('Editar item')">
    <div class="min-h-full text-base-content">
        <div class="mx-auto flex w-full max-w-4xl flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8">
            <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="badge badge-secondary badge-outline mb-3">Admin</div>
                    <h1 class="text-2xl font-bold text-neutral sm:text-3xl">Editar item</h1>
                </div>
                <x-link-button href="{{ route('menu-items.index') }}" class="min-h-11">Voltar</x-link-button>
            </section>

            @if ($errors->any())
                <div class="alert alert-error"><span>{{ $errors->first() }}</span></div>
            @endif

            <x-card>
                <x-form :action="route('menu-items.update', $item)" post>
                    @method('PATCH')

                    <label class="form-control">
                        <x-input-label value="Nome do item" />
                        <x-text-input name="name" value="{{ old('name', $item->name) }}" required />
                    </label>

                    <label class="form-control">
                        <x-input-label value="Valor" />
                        <x-text-input name="price" type="number" min="0" step="0.01" value="{{ old('price', $item->price) }}" required />
                    </label>

                    <label class="form-control">
                        <x-input-label value="Descrição" />
                        <textarea name="description" class="textarea textarea-bordered min-h-24 bg-base-100">{{ old('description', $item->description) }}</textarea>
                    </label>

                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="hidden" name="active" value="0">
                        <input type="checkbox" name="active" value="1" class="toggle toggle-primary" @checked(old('active', $item->active ? '1' : '0') === '1')>
                        <span class="label-text">Disponível para atendentes</span>
                    </label>

                    <div class="flex flex-col-reverse gap-3 border-t border-base-300 pt-4 sm:flex-row sm:justify-end">
                        <x-secondary-button type="reset">Limpar</x-secondary-button>
                        <x-primary-button type="submit">Salvar alterações</x-primary-button>
                    </div>
                </x-form>
            </x-card>
        </div>
    </div>
</x-layouts::app>
