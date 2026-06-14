@php
    $money = fn ($value) => 'R$ '.number_format((float) $value, 2, ',', '.');
    $items = collect($menuItems->items());
    $menuMetrics = [
        [
            'label' => 'Itens nesta página',
            'value' => $items->count(),
            'description' => 'Produtos cadastrados',
            'icon' => 'book-open',
            'accent' => 'text-primary bg-primary/10 ring-primary/15',
        ],
        [
            'label' => 'Ativos',
            'value' => $items->where('active', true)->count(),
            'description' => 'Aparecem na nova comanda',
            'icon' => 'check-circle',
            'accent' => 'text-success bg-success/10 ring-success/15',
        ],
        [
            'label' => 'Inativos',
            'value' => $items->where('active', false)->count(),
            'description' => 'Ocultos do atendimento',
            'icon' => 'x-circle',
            'accent' => 'text-error bg-error/10 ring-error/15',
        ],
        [
            'label' => 'Valor médio',
            'value' => $items->count() ? $money($items->avg('price')) : $money(0),
            'description' => 'Média da página atual',
            'icon' => 'banknotes',
            'accent' => 'text-secondary bg-secondary/10 ring-secondary/15',
        ],
    ];
@endphp

<x-layouts::app :title="__('Itens da comanda')">
    <div class="min-h-full text-base-content">
        <div class="mx-auto flex w-full max-w-[100rem] flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8 2xl:px-10">
            <section class="rounded-lg border border-base-300/80 bg-base-100/90 p-5 shadow-sm backdrop-blur sm:p-6">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div class="min-w-0">
                        <div class="mb-3 inline-flex items-center gap-2 rounded-md border border-secondary/25 bg-secondary/10 px-3 py-1 text-xs font-semibold uppercase tracking-normal text-secondary">
                            <span class="size-1.5 rounded-full bg-secondary"></span>
                            Admin
                        </div>
                        <h1 class="text-3xl font-bold tracking-normal text-neutral sm:text-4xl">Itens da comanda</h1>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-base-content/65">
                            Cadastre os produtos que o atendente poderá selecionar ao criar uma comanda.
                        </p>
                    </div>

                    <x-link-button href="{{ route('menu-items.create') }}" class="min-h-11 gap-2 whitespace-nowrap px-4">
                        <flux:icon.plus class="size-4" />
                        Novo item
                    </x-link-button>
                </div>
            </section>

            @if (session('status'))
                <div class="alert alert-success rounded-lg border border-success/20 bg-success/10 text-success">
                    <flux:icon.check-circle class="size-5" />
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($menuMetrics as $metric)
                    <article class="rounded-lg border border-base-300/80 bg-base-100 p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-base-content/60">{{ $metric['label'] }}</p>
                                <p class="mt-2 truncate text-3xl font-bold tracking-normal text-neutral sm:text-4xl">{{ $metric['value'] }}</p>
                            </div>

                            <x-icon-mark :icon="$metric['icon']" :accent="$metric['accent']" class="size-7" />
                        </div>

                        <p class="mt-4 border-t border-base-300/70 pt-3 text-sm text-base-content/60">{{ $metric['description'] }}</p>
                    </article>
                @endforeach
            </section>

            <x-card bodyClass="p-0">
                <div class="flex flex-col gap-3 border-b border-base-300/80 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                    <div>
                        <h2 class="text-lg font-semibold text-neutral">Catálogo do atendimento</h2>
                        <p class="text-sm text-base-content/55">Produtos, descrições, preços e disponibilidade.</p>
                    </div>

                    <div class="inline-flex items-center gap-2 rounded-md border border-base-300 bg-base-200/70 px-3 py-2 text-sm text-base-content/65">
                        <flux:icon.book-open class="size-4 text-primary" />
                        {{ $items->count() }} registro{{ $items->count() === 1 ? '' : 's' }}
                    </div>
                </div>

                @if ($menuItems->isEmpty())
                    <div class="flex flex-col items-center gap-4 p-10 text-center">
                        <flux:icon.book-open class="size-8 text-primary" />
                        <div>
                            <h2 class="text-lg font-semibold text-neutral">Nenhum item cadastrado</h2>
                            <p class="mt-1 max-w-md text-sm text-base-content/65">
                                Crie os itens do cardápio para facilitar o atendimento.
                            </p>
                        </div>
                        <x-link-button href="{{ route('menu-items.create') }}" class="gap-2">
                            <flux:icon.plus class="size-4" />
                            Cadastrar item
                        </x-link-button>
                    </div>
                @else
                    <div class="grid gap-3 p-4 md:hidden">
                        @foreach ($menuItems as $item)
                            <a href="{{ route('menu-items.edit', $item) }}" class="rounded-lg border border-base-300/80 bg-base-100 p-4 shadow-sm" wire:navigate>
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-neutral">{{ $item->name }}</p>
                                        <p class="truncate text-sm text-base-content/60">{{ $item->description ?: 'Sem descrição' }}</p>
                                    </div>
                                    <span class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $item->active ? 'bg-success/10 text-success ring-success/20' : 'bg-base-200 text-base-content/70 ring-base-300' }}">
                                        <span class="size-1.5 rounded-full {{ $item->active ? 'bg-success' : 'bg-base-content/35' }}"></span>
                                        {{ $item->active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </div>
                                <div class="mt-4 rounded-md bg-base-200/70 p-3 text-sm">
                                    <span class="text-xs text-base-content/50">Valor</span>
                                    <strong class="mt-1 block text-base text-neutral">{{ $money($item->price) }}</strong>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="hidden overflow-x-auto md:block">
                        <table class="min-w-full text-sm">
                            <thead class="bg-base-200/70 text-left text-xs font-semibold uppercase tracking-normal text-base-content/55">
                                <tr>
                                    <th class="px-5 py-4">Item</th>
                                    <th class="px-5 py-4">Descrição</th>
                                    <th class="px-5 py-4">Valor</th>
                                    <th class="px-5 py-4">Status</th>
                                    <th class="px-5 py-4 text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-base-300/70">
                                @foreach ($menuItems as $item)
                                    <tr class="transition hover:bg-primary/5">
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-3">
                                                <flux:icon.book-open class="size-6 text-primary" />
                                                <div>
                                                    <p class="font-semibold text-neutral">{{ $item->name }}</p>
                                                    <p class="text-xs text-base-content/50">Item do cardápio</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="max-w-xl px-5 py-4 text-base-content/70">
                                            <span class="line-clamp-2">{{ $item->description ?: '-' }}</span>
                                        </td>
                                        <td class="px-5 py-4 font-semibold text-neutral">{{ $money($item->price) }}</td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $item->active ? 'bg-success/10 text-success ring-success/20' : 'bg-base-200 text-base-content/70 ring-base-300' }}">
                                                <span class="size-1.5 rounded-full {{ $item->active ? 'bg-success' : 'bg-base-content/35' }}"></span>
                                                {{ $item->active ? 'Ativo' : 'Inativo' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            <a href="{{ route('menu-items.edit', $item) }}" class="btn btn-primary btn-soft btn-sm gap-2" wire:navigate>
                                                <flux:icon.pencil-square class="size-4" />
                                                Editar
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($menuItems->hasPages())
                        <div class="border-t border-base-300/80 p-4">
                            {{ $menuItems->links() }}
                        </div>
                    @endif
                @endif
            </x-card>
        </div>
    </div>
</x-layouts::app>
