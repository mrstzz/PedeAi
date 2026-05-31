@php
    $money = fn ($value) => 'R$ '.number_format((float) $value, 2, ',', '.');
@endphp

<x-layouts::app :title="__('Itens da comanda')">
    <div class="min-h-full bg-base-200 text-base-content">
        <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8">
            <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="badge badge-secondary badge-outline mb-3">Admin</div>
                    <h1 class="text-2xl font-bold text-neutral sm:text-3xl">Itens da comanda</h1>
                    <p class="mt-2 max-w-2xl text-sm text-base-content/70">
                        Cadastre os produtos que o atendente podera selecionar ao criar uma comanda.
                    </p>
                </div>

                <x-link-button href="{{ route('menu-items.create') }}" class="min-h-11">
                    Novo item
                </x-link-button>
            </section>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <x-card bodyClass="p-0">
                @if ($menuItems->isEmpty())
                    <div class="flex flex-col items-center gap-4 p-10 text-center">
                        <div class="badge badge-outline badge-primary">Sem itens</div>
                        <div>
                            <h2 class="text-lg font-semibold text-neutral">Nenhum item cadastrado</h2>
                            <p class="mt-1 max-w-md text-sm text-base-content/65">
                                Crie os itens do cardapio para facilitar o atendimento.
                            </p>
                        </div>
                        <x-link-button href="{{ route('menu-items.create') }}">Cadastrar item</x-link-button>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Descricao</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($menuItems as $item)
                                    <tr class="hover">
                                        <td class="font-semibold text-neutral">{{ $item->name }}</td>
                                        <td>{{ $item->description ?: '-' }}</td>
                                        <td>{{ $money($item->price) }}</td>
                                        <td>
                                            <span class="badge {{ $item->active ? 'badge-success' : 'badge-ghost' }}">
                                                {{ $item->active ? 'Ativo' : 'Inativo' }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ route('menu-items.edit', $item) }}" class="btn btn-ghost btn-sm" wire:navigate>Editar</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($menuItems->hasPages())
                        <div class="border-t border-base-300 p-4">
                            {{ $menuItems->links() }}
                        </div>
                    @endif
                @endif
            </x-card>
        </div>
    </div>
</x-layouts::app>
