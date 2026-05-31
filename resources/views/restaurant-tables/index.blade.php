@php
    $statusLabels = [
        'disponivel' => 'Disponivel',
        'ocupada' => 'Ocupada',
        'reservada' => 'Reservada',
        'manutencao' => 'Manutencao',
    ];

    $statusBadges = [
        'disponivel' => 'badge-success',
        'ocupada' => 'badge-error',
        'reservada' => 'badge-warning',
        'manutencao' => 'badge-ghost',
    ];
@endphp

<x-layouts::app :title="__('Mesas')">
    <div class="min-h-full bg-base-200 text-base-content">
        <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8">
            <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="badge badge-secondary badge-outline mb-3">Admin</div>
                    <h1 class="text-2xl font-bold text-neutral sm:text-3xl">Mesas</h1>
                    <p class="mt-2 max-w-2xl text-sm text-base-content/70">
                        Cadastre as mesas que poderao ser selecionadas ao abrir comandas.
                    </p>
                </div>

                <x-link-button href="{{ route('restaurant-tables.create') }}" class="min-h-11">
                    Nova mesa
                </x-link-button>
            </section>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <x-card bodyClass="p-0">
                @if ($tables->isEmpty())
                    <div class="flex flex-col items-center gap-4 p-10 text-center">
                        <div class="badge badge-outline badge-primary">Sem mesas</div>
                        <div>
                            <h2 class="text-lg font-semibold text-neutral">Nenhuma mesa cadastrada</h2>
                            <p class="mt-1 max-w-md text-sm text-base-content/65">
                                Cadastre as mesas para que elas aparecam na abertura de comandas conforme disponibilidade.
                            </p>
                        </div>
                        <x-link-button href="{{ route('restaurant-tables.create') }}">Cadastrar mesa</x-link-button>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Mesa</th>
                                    <th>Capacidade</th>
                                    <th>Status</th>
                                    <th>Comandas abertas</th>
                                    <th>Reservas confirmadas</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tables as $table)
                                    <tr class="hover">
                                        <td class="font-semibold text-neutral">Mesa {{ $table->identifier }}</td>
                                        <td>{{ $table->capacity }} lugares</td>
                                        <td>
                                            <span class="badge {{ $statusBadges[$table->status] ?? 'badge-neutral' }}">
                                                {{ $statusLabels[$table->status] ?? $table->status }}
                                            </span>
                                        </td>
                                        <td>{{ $table->open_tickets_count }}</td>
                                        <td>{{ $table->active_reservations_count }}</td>
                                        <td class="text-right">
                                            <a href="{{ route('restaurant-tables.edit', $table) }}" class="btn btn-ghost btn-sm" wire:navigate>Editar</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($tables->hasPages())
                        <div class="border-t border-base-300 p-4">
                            {{ $tables->links() }}
                        </div>
                    @endif
                @endif
            </x-card>
        </div>
    </div>
</x-layouts::app>
