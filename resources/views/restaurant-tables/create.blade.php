<x-layouts::app :title="__('Nova mesa')">
    <div class="min-h-full text-base-content">
        <div class="mx-auto flex w-full max-w-[100rem] flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8 2xl:px-10">
            <x-page-header
                eyebrow="Admin"
                title="Nova mesa"
                description="Defina identificação, capacidade e status inicial da mesa."
                icon="table-cells"
            >
                <x-link-button href="{{ route('restaurant-tables.index') }}" class="min-h-11 gap-2 whitespace-nowrap px-4">
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
                            <h2 class="text-lg font-semibold text-neutral">Dados da mesa</h2>
                            <p class="text-sm text-base-content/55">Identificação operacional e disponibilidade inicial.</p>
                        </div>

                        <flux:icon.table-cells class="size-6 text-primary" />
                    </div>

                    <x-form :action="route('restaurant-tables.store')" post>
                        <section class="grid gap-4 sm:grid-cols-2">
                            <label class="form-control">
                                <x-input-label value="Identificação" />
                                <x-text-input
                                    name="identifier"
                                    value="{{ old('identifier') }}"
                                    placeholder="Ex: 1, 02, Área externa 4"
                                    class="min-h-11"
                                    required
                                />
                            </label>

                            <label class="form-control">
                                <x-input-label value="Capacidade" />
                                <x-text-input
                                    name="capacity"
                                    type="number"
                                    min="1"
                                    max="100"
                                    step="1"
                                    value="{{ old('capacity', 4) }}"
                                    placeholder="4"
                                    class="min-h-11"
                                    required
                                />
                            </label>
                        </section>

                        <label class="form-control">
                            <x-input-label value="Status inicial" />
                            <select name="status" class="select select-bordered min-h-11 w-full bg-base-100">
                                @foreach ($statuses as $status => $label)
                                    <option value="{{ $status }}" @selected(old('status', 'disponivel') === $status)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <x-alert-message tone="info">
                            Somente mesas disponíveis e sem reserva confirmada no horário aparecem na abertura de comanda.
                        </x-alert-message>

                        <div class="flex flex-col-reverse gap-3 border-t border-base-300/80 pt-4 sm:flex-row sm:justify-end">
                            <x-secondary-button type="reset" class="min-h-11 gap-2">
                                <flux:icon.arrow-path class="size-4" />
                                Limpar
                            </x-secondary-button>
                            <x-primary-button type="submit" class="min-h-11 gap-2">
                                <flux:icon.check class="size-4" />
                                Salvar mesa
                            </x-primary-button>
                        </div>
                    </x-form>
                </x-card>

                <aside class="flex flex-col gap-4">
                    <x-metric-card
                        label="Status padrão"
                        value="Disponível"
                        description="Mesa pronta para atendimento."
                        icon="check-circle"
                        accent="text-success bg-success/10 ring-success/15"
                    />
                    <x-card>
                        <div class="flex items-start gap-3">
                            <flux:icon.light-bulb class="size-6 shrink-0 text-secondary" />
                            <p class="text-sm leading-6 text-base-content/65">
                                Use identificações curtas para facilitar a leitura no salão, como 1, 2, Varanda 1 ou Balcão.
                            </p>
                        </div>
                    </x-card>
                </aside>
            </section>
        </div>
    </div>
</x-layouts::app>
