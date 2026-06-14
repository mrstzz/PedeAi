<x-layouts::app :title="__('Editar mesa')">
    <div class="min-h-full text-base-content">
        <div class="mx-auto flex w-full max-w-[100rem] flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8 2xl:px-10">
            <x-page-header
                eyebrow="Admin"
                title="Editar mesa"
                description="Atualize identificação, capacidade e status operacional da mesa."
                icon="table-cells"
            >
                <x-link-button href="{{ route('restaurant-tables.index') }}" class="min-h-11 gap-2 whitespace-nowrap px-4">
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
                        <div>
                            <h2 class="text-lg font-semibold text-neutral">Mesa {{ $table->identifier }}</h2>
                            <p class="text-sm text-base-content/55">Ajustes aplicados ao cadastro da mesa.</p>
                        </div>

                        <x-status-pill :label="$statuses[$table->status] ?? $table->status" :tone="$table->status === 'disponivel' ? 'success' : ($table->status === 'ocupada' ? 'error' : ($table->status === 'reservada' ? 'warning' : 'neutral'))" />
                    </div>

                    <x-form :action="route('restaurant-tables.update', $table)" post>
                        @method('PATCH')

                        <section class="grid gap-4 sm:grid-cols-2">
                            <label class="form-control">
                                <x-input-label value="Identificação" />
                                <x-text-input name="identifier" value="{{ old('identifier', $table->identifier) }}" class="min-h-11" required />
                            </label>

                            <label class="form-control">
                                <x-input-label value="Capacidade" />
                                <x-text-input name="capacity" type="number" min="1" max="100" step="1" value="{{ old('capacity', $table->capacity) }}" class="min-h-11" required />
                            </label>
                        </section>

                        <label class="form-control">
                            <x-input-label value="Status" />
                            <select name="status" class="select select-bordered min-h-11 w-full bg-base-100">
                                @foreach ($statuses as $status => $label)
                                    <option value="{{ $status }}" @selected(old('status', $table->status) === $status)>{{ $label }}</option>
                                @endforeach
                            </select>
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
                        label="Capacidade atual"
                        :value="$table->capacity"
                        description="Lugares configurados."
                        icon="users"
                        accent="text-secondary bg-secondary/10 ring-secondary/15"
                    />
                    <x-card>
                        <div class="flex items-start gap-3">
                            <flux:icon.information-circle class="size-6 shrink-0 text-primary" />
                            <p class="text-sm leading-6 text-base-content/65">
                                Alterar uma mesa ocupada pode impactar a abertura de novas comandas. Confirme o status antes de salvar.
                            </p>
                        </div>
                    </x-card>
                </aside>
            </section>
        </div>
    </div>
</x-layouts::app>
