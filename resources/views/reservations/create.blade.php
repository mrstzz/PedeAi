<x-layouts::app :title="__('Nova reserva')">
    <div class="min-h-full bg-base-200 text-base-content">
        <div class="mx-auto flex w-full max-w-4xl flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8">
            <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="badge badge-secondary badge-outline mb-3">Atendimento</div>
                    <h1 class="text-2xl font-bold text-neutral sm:text-3xl">Nova reserva</h1>
                    <p class="mt-2 max-w-2xl text-sm text-base-content/70">
                        Reserve uma mesa para o cliente. A mesa ficara bloqueada para comandas normais.
                    </p>
                </div>

                <x-link-button href="{{ route('reservations.index') }}" class="min-h-11">
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

            @if ($tables->isEmpty())
                <div class="alert alert-warning">
                    <div>
                        <h2 class="font-semibold">Nenhuma mesa disponivel</h2>
                        <p class="text-sm">Cadastre ou libere uma mesa antes de registrar novas reservas.</p>
                    </div>
                </div>
            @endif

            <x-card>
                <x-form :action="route('reservations.store')" post>
                    <section class="grid gap-4 sm:grid-cols-2">
                        <label class="form-control">
                            <x-input-label value="Cliente" />
                            <x-text-input name="customer_name" value="{{ old('customer_name') }}" placeholder="Nome do cliente" required />
                        </label>

                        <label class="form-control">
                            <x-input-label value="Telefone" />
                            <x-text-input name="customer_phone" value="{{ old('customer_phone') }}" placeholder="(00) 00000-0000" />
                        </label>

                        <label class="form-control">
                            <x-input-label value="Mesa" />
                            <select name="restaurant_table_id" class="select select-bordered w-full bg-base-100" @disabled($tables->isEmpty())>
                                <option value="">Selecione uma mesa</option>
                                @foreach ($tables as $table)
                                    <option value="{{ $table->id }}" @selected((string) old('restaurant_table_id') === (string) $table->id)>
                                        Mesa {{ $table->identifier }} - {{ $table->capacity }} lugares
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <label class="form-control">
                            <x-input-label value="Quantidade de pessoas" />
                            <x-text-input name="party_size" type="number" min="1" max="100" step="1" value="{{ old('party_size') }}" placeholder="Ex: 4" />
                        </label>

                        <label class="form-control">
                            <x-input-label value="Data e hora" />
                            <x-text-input
                                name="reserved_at"
                                type="datetime-local"
                                value="{{ old('reserved_at') }}"
                                min="{{ now('America/Sao_Paulo')->format('Y-m-d\TH:i') }}"
                                required
                            />
                        </label>

                        <label class="form-control">
                            <x-input-label value="Duracao" />
                            <select name="duration_minutes" class="select select-bordered w-full bg-base-100">
                                @foreach ([60, 90, 120, 180, 240] as $minutes)
                                    <option value="{{ $minutes }}" @selected((int) old('duration_minutes', 120) === $minutes)>
                                        {{ $minutes }} minutos
                                    </option>
                                @endforeach
                            </select>
                        </label>
                    </section>

                    <label class="form-control">
                        <x-input-label value="Observacoes" />
                        <textarea
                            name="notes"
                            class="textarea textarea-bordered min-h-24 bg-base-100"
                            placeholder="Ex: aniversario, mesa proxima ao palco, preferencias..."
                        >{{ old('notes') }}</textarea>
                    </label>

                    <div class="alert alert-info">
                        <span>Depois de salva, a mesa fica reservada e so podera abrir comanda pela reserva correspondente.</span>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-base-300 pt-4 sm:flex-row sm:justify-end">
                        <x-secondary-button type="reset">Limpar</x-secondary-button>
                        <x-primary-button type="submit" :disabled="$tables->isEmpty()">Salvar reserva</x-primary-button>
                    </div>
                </x-form>
            </x-card>
        </div>
    </div>
</x-layouts::app>
