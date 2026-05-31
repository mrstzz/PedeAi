<x-layouts::app :title="__('Editar reserva')">
    <div class="min-h-full bg-base-200 text-base-content">
        <div class="mx-auto flex w-full max-w-4xl flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8">
            <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="badge badge-secondary badge-outline mb-3">Atendimento</div>
                    <h1 class="text-2xl font-bold text-neutral sm:text-3xl">Editar reserva</h1>
                </div>
                <x-link-button href="{{ route('reservations.index') }}" class="min-h-11">Voltar</x-link-button>
            </section>

            @if ($errors->any())
                <div class="alert alert-error"><span>{{ $errors->first() }}</span></div>
            @endif

            <x-card>
                <x-form :action="route('reservations.update', $reservation)" post>
                    @method('PATCH')

                    <section class="grid gap-4 sm:grid-cols-2">
                        <label class="form-control">
                            <x-input-label value="Cliente" />
                            <x-text-input name="customer_name" value="{{ old('customer_name', $reservation->customer_name) }}" required />
                        </label>

                        <label class="form-control">
                            <x-input-label value="Telefone" />
                            <x-text-input name="customer_phone" value="{{ old('customer_phone', $reservation->customer_phone) }}" />
                        </label>

                        <label class="form-control">
                            <x-input-label value="Mesa" />
                            <select name="restaurant_table_id" class="select select-bordered w-full bg-base-100">
                                @foreach ($tables as $table)
                                    <option value="{{ $table->id }}" @selected((int) old('restaurant_table_id', $reservation->restaurant_table_id) === (int) $table->id)>
                                        Mesa {{ $table->identifier }} - {{ $table->capacity }} lugares
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <label class="form-control">
                            <x-input-label value="Quantidade de pessoas" />
                            <x-text-input name="party_size" type="number" min="1" max="100" step="1" value="{{ old('party_size', $reservation->party_size) }}" />
                        </label>

                        <label class="form-control">
                            <x-input-label value="Data e hora" />
                            <x-text-input name="reserved_at" type="datetime-local" value="{{ old('reserved_at', $reservation->reserved_at->timezone('America/Sao_Paulo')->format('Y-m-d\TH:i')) }}" required />
                        </label>

                        <label class="form-control">
                            <x-input-label value="Duracao" />
                            <x-text-input name="duration_minutes" type="number" min="30" max="480" step="30" value="{{ old('duration_minutes', $reservation->duration_minutes) }}" required />
                        </label>
                    </section>

                    <label class="form-control">
                        <x-input-label value="Observacoes" />
                        <textarea name="notes" class="textarea textarea-bordered min-h-24 bg-base-100">{{ old('notes', $reservation->notes) }}</textarea>
                    </label>

                    <div class="flex flex-col-reverse gap-3 border-t border-base-300 pt-4 sm:flex-row sm:justify-end">
                        <x-secondary-button type="reset">Limpar</x-secondary-button>
                        <x-primary-button type="submit">Salvar alteracoes</x-primary-button>
                    </div>
                </x-form>
            </x-card>
        </div>
    </div>
</x-layouts::app>
