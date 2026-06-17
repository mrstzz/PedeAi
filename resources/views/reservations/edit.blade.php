<x-layouts::app :title="__('Editar reserva')">
    <div class="min-h-full text-base-content">
        <div class="mx-auto flex w-full max-w-[100rem] flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8 2xl:px-10">
            <x-page-header
                eyebrow="Atendimento"
                title="Editar reserva"
                description="Atualize os dados da reserva e mantenha o bloqueio da mesa consistente."
                icon="calendar-days"
            >
                <x-link-button href="{{ route('reservations.index') }}" class="min-h-11 gap-2 whitespace-nowrap px-4">
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
                            <h2 class="truncate text-lg font-semibold text-neutral">{{ $reservation->customer_name }}</h2>
                            <p class="text-sm text-base-content/55">Reserva #{{ $reservation->id }}</p>
                        </div>

                        <x-status-pill label="Em edição" tone="primary" />
                    </div>

                    <x-form :action="route('reservations.update', $reservation)" post>
                        @method('PATCH')

                        <section class="grid gap-4 sm:grid-cols-2">
                            <label class="form-control">
                                <x-input-label value="Cliente" />
                                <x-text-input name="customer_name" value="{{ old('customer_name', $reservation->customer_name) }}" class="min-h-11" required />
                            </label>

                            <label class="form-control">
                                <x-input-label value="Telefone" />
                                <x-text-input name="customer_phone" value="{{ old('customer_phone', $reservation->customer_phone) }}" class="min-h-11" />
                            </label>

                            <label class="form-control">
                                <x-input-label value="Mesa" />
                                <select name="restaurant_table_id" class="select select-bordered min-h-11 w-full bg-base-100">
                                    @foreach ($tables as $table)
                                        <option value="{{ $table->id }}" @selected((int) old('restaurant_table_id', $reservation->restaurant_table_id) === (int) $table->id)>
                                            Mesa {{ $table->identifier }} - {{ $table->capacity }} lugares
                                        </option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="form-control">
                                <x-input-label value="Quantidade de pessoas" />
                                <x-text-input name="party_size" type="number" min="1" max="100" step="1" value="{{ old('party_size', $reservation->party_size) }}" class="min-h-11" />
                            </label>

                            <label class="form-control">
                                <x-input-label value="Data e hora" />
                                <x-text-input name="reserved_at" type="datetime-local" value="{{ old('reserved_at', $reservation->reserved_at->timezone('America/Sao_Paulo')->format('Y-m-d\TH:i')) }}" class="min-h-11" required />
                            </label>

                            <label class="form-control">
                                <x-input-label value="Duração" />
                                <x-text-input name="duration_minutes" type="number" min="30" max="480" step="30" value="{{ old('duration_minutes', $reservation->duration_minutes) }}" class="min-h-11" required />
                            </label>
                        </section>

                        <label class="form-control">
                            <x-input-label value="Observações" />
                            <textarea name="notes" class="textarea textarea-bordered min-h-28 w-full bg-base-100">{{ old('notes', $reservation->notes) }}</textarea>
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
                        label="Duração atual"
                        value="{{ $reservation->duration_minutes }} min"
                        description="Tempo de bloqueio da mesa."
                        icon="clock"
                        accent="text-info bg-info/10 ring-info/15"
                    />
                    <x-card>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between gap-3 rounded-md bg-base-200/70 p-3">
                                <span class="text-base-content/60">Mesa</span>
                                <strong class="text-neutral">{{ $reservation->restaurantTable?->identifier ?? '-' }}</strong>
                            </div>
                            <div class="flex justify-between gap-3 rounded-md bg-base-200/70 p-3">
                                <span class="text-base-content/60">Horário</span>
                                <strong class="text-neutral">{{ $reservation->reserved_at->timezone('America/Sao_Paulo')->format('d/m H:i') }}</strong>
                            </div>
                        </div>
                    </x-card>
                </aside>
            </section>
        </div>
    </div>
</x-layouts::app>
