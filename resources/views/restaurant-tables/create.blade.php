<x-layouts::app :title="__('Nova mesa')">
    <div class="min-h-full text-base-content">
        <div class="mx-auto flex w-full max-w-4xl flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8">
            <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="badge badge-secondary badge-outline mb-3">Admin</div>
                    <h1 class="text-2xl font-bold text-neutral sm:text-3xl">Nova mesa</h1>
                    <p class="mt-2 max-w-2xl text-sm text-base-content/70">
                        Defina identificacao, capacidade e status inicial da mesa.
                    </p>
                </div>

                <x-link-button href="{{ route('restaurant-tables.index') }}" class="min-h-11">
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
                <x-form :action="route('restaurant-tables.store')" post>
                    <section class="grid gap-4 sm:grid-cols-2">
                        <label class="form-control">
                            <x-input-label value="Identificacao" />
                            <x-text-input
                                name="identifier"
                                value="{{ old('identifier') }}"
                                placeholder="Ex: 1, 02, Area externa 4"
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
                                required
                            />
                        </label>
                    </section>

                    <label class="form-control">
                        <x-input-label value="Status inicial" />
                        <select name="status" class="select select-bordered w-full bg-base-100">
                            @foreach ($statuses as $status => $label)
                                <option value="{{ $status }}" @selected(old('status', 'disponivel') === $status)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <div class="alert alert-info">
                        <span>Somente mesas com status disponivel e sem reserva confirmada no horario aparecem na abertura de comanda.</span>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-base-300 pt-4 sm:flex-row sm:justify-end">
                        <x-secondary-button type="reset">Limpar</x-secondary-button>
                        <x-primary-button type="submit">Salvar mesa</x-primary-button>
                    </div>
                </x-form>
            </x-card>
        </div>
    </div>
</x-layouts::app>
