<x-layouts::app :title="__('Nova reserva')">
    <div class="min-h-full text-base-content">
        <div class="mx-auto flex w-full max-w-[100rem] flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8 2xl:px-10">
            <x-page-header
                eyebrow="Atendimento"
                title="Nova reserva"
                description="Reserve uma mesa para o cliente. A mesa ficará bloqueada para comandas normais."
                icon="calendar-days"
            >
                <x-link-button href="{{ route('reservations.index') }}" class="min-h-11 gap-2 whitespace-nowrap px-4">
                    <flux:icon.arrow-left class="size-4" />
                    Voltar
                </x-link-button>
            </x-page-header>

            @if ($errors->any())
                <x-alert-message tone="error" title="Revise os dados informados">
                    {{ $errors->first() }}
                </x-alert-message>
            @endif

            @if ($tables->isEmpty())
                <x-alert-message tone="warning" title="Nenhuma mesa disponível">
                    Cadastre ou libere uma mesa antes de registrar novas reservas.
                </x-alert-message>
            @endif

            <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]">
                <x-card>
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-neutral">Dados da reserva</h2>
                            <p class="text-sm text-base-content/55">Cliente, mesa, horário e duração do bloqueio.</p>
                        </div>

                        <div class="grid size-10 place-items-center rounded-lg bg-primary/10 text-primary ring-1 ring-primary/15">
                            <flux:icon.calendar-days class="size-5" />
                        </div>
                    </div>

                    <x-form :action="route('reservations.store')" post>
                        <section class="grid gap-4 sm:grid-cols-2">
                            <label class="form-control">
                                <x-input-label value="Cliente" />
                                <x-text-input name="customer_name" value="{{ old('customer_name') }}" placeholder="Nome do cliente" class="min-h-11" required />
                            </label>

                            <label class="form-control">
                                <x-input-label value="Telefone" />
                                <x-text-input name="customer_phone" value="{{ old('customer_phone') }}" placeholder="(00) 00000-0000" class="min-h-11" />
                            </label>

                            <label class="form-control">
                                <x-input-label value="Mesa" />
                                <select name="restaurant_table_id" class="select select-bordered min-h-11 w-full bg-base-100" @disabled($tables->isEmpty())>
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
                                <x-text-input name="party_size" type="number" min="1" max="100" step="1" value="{{ old('party_size') }}" placeholder="Ex: 4" class="min-h-11" />
                            </label>

                            <label class="form-control">
                                <x-input-label value="Data e hora" />
                                <x-text-input
                                    name="reserved_at"
                                    type="datetime-local"
                                    value="{{ old('reserved_at') }}"
                                    min="{{ now('America/Sao_Paulo')->format('Y-m-d\TH:i') }}"
                                    class="min-h-11"
                                    required
                                />
                            </label>

                            <label class="form-control">
                                <x-input-label value="Duração" />
                                <select name="duration_minutes" class="select select-bordered min-h-11 w-full bg-base-100">
                                    @foreach ([60, 90, 120, 180, 240] as $minutes)
                                        <option value="{{ $minutes }}" @selected((int) old('duration_minutes', 120) === $minutes)>
                                            {{ $minutes }} minutos
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                        </section>

                        <label class="form-control">
                            <x-input-label value="Observações" />
                            <textarea
                                name="notes"
                                class="textarea textarea-bordered min-h-28 w-full bg-base-100"
                                placeholder="Ex: aniversário, mesa próxima ao palco, preferências..."
                            >{{ old('notes') }}</textarea>
                        </label>

                        <x-alert-message tone="info">
                            Depois de salva, a mesa fica reservada e só poderá abrir comanda pela reserva correspondente.
                        </x-alert-message>

                        <div class="flex flex-col-reverse gap-3 border-t border-base-300/80 pt-4 sm:flex-row sm:justify-end">
                            <x-secondary-button type="reset" class="min-h-11 gap-2">
                                <flux:icon.arrow-path class="size-4" />
                                Limpar
                            </x-secondary-button>
                            <x-primary-button type="submit" class="min-h-11 gap-2" :disabled="$tables->isEmpty()">
                                <flux:icon.check class="size-4" />
                                Salvar reserva
                            </x-primary-button>
                        </div>
                    </x-form>
                </x-card>

                <aside class="flex flex-col gap-4">
                    <x-metric-card
                        label="Mesas disponíveis"
                        :value="$tables->count()"
                        description="Podem ser reservadas agora."
                        icon="table-cells"
                        accent="text-success bg-success/10 ring-success/15"
                    />
                    <x-card>
                        <div class="flex items-start gap-3">
                            <div class="grid size-10 shrink-0 place-items-center rounded-lg bg-secondary/10 text-secondary ring-1 ring-secondary/15">
                                <flux:icon.light-bulb class="size-5" />
                            </div>
                            <p class="text-sm leading-6 text-base-content/65">
                                Informe telefone e observações quando houver preferências importantes para a recepção.
                            </p>
                        </div>
                    </x-card>
                </aside>
            </section>
        </div>
    </div>
</x-layouts::app>
