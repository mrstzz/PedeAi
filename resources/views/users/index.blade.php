@php
    $userItems = collect($users->items());
    $userMetrics = [
        [
            'label' => 'Usuarios nesta pagina',
            'value' => $userItems->count(),
            'description' => 'Contas cadastradas',
            'icon' => 'users',
            'accent' => 'text-primary bg-primary/10 ring-primary/15',
        ],
        [
            'label' => 'Administradores',
            'value' => $userItems->filter(fn ($user) => $user->isAdmin())->count(),
            'description' => 'Protegidos por regra',
            'icon' => 'shield-check',
            'accent' => 'text-secondary bg-secondary/10 ring-secondary/15',
        ],
        [
            'label' => 'Alteraveis',
            'value' => $userItems->reject(fn ($user) => $user->is(auth()->user()) || $user->isAdmin())->count(),
            'description' => 'Permissao editavel aqui',
            'icon' => 'pencil-square',
            'accent' => 'text-success bg-success/10 ring-success/15',
        ],
        [
            'label' => 'Roles disponiveis',
            'value' => $roles->count(),
            'description' => 'Sem administrador',
            'icon' => 'key',
            'accent' => 'text-info bg-info/10 ring-info/15',
        ],
    ];
@endphp

<x-layouts::app :title="__('Usuarios')">
    <div class="min-h-full text-base-content">
        <div class="mx-auto flex w-full max-w-[100rem] flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8 2xl:px-10">
            <section class="rounded-lg border border-base-300/80 bg-base-100/90 p-5 shadow-sm backdrop-blur sm:p-6">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div class="min-w-0">
                        <div class="mb-3 inline-flex items-center gap-2 rounded-md border border-secondary/25 bg-secondary/10 px-3 py-1 text-xs font-semibold uppercase tracking-normal text-secondary">
                            <span class="size-1.5 rounded-full bg-secondary"></span>
                            Administracao
                        </div>
                        <h1 class="text-3xl font-bold tracking-normal text-neutral sm:text-4xl">Usuarios</h1>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-base-content/65">
                            Gerencie as permissoes dos usuarios. A permissao de administrador nao pode ser atribuida por esta tela.
                        </p>
                    </div>

                    <div class="inline-flex items-center gap-2 rounded-md border border-base-300 bg-base-200/70 px-3 py-2 text-sm text-base-content/65">
                        <flux:icon.shield-check class="size-4 text-primary" />
                        Controle de acesso
                    </div>
                </div>
            </section>

            @if (session('status'))
                <div class="alert alert-success rounded-lg border border-success/20 bg-success/10 text-success">
                    <flux:icon.check-circle class="size-5" />
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error rounded-lg border border-error/20 bg-error/10 text-error">
                    <flux:icon.exclamation-triangle class="size-5" />
                    <div>
                        <h2 class="font-semibold">Revise os dados informados</h2>
                        <p class="text-sm">{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($userMetrics as $metric)
                    <article class="rounded-lg border border-base-300/80 bg-base-100 p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-base-content/60">{{ $metric['label'] }}</p>
                                <p class="mt-2 text-4xl font-bold tracking-normal text-neutral">{{ $metric['value'] }}</p>
                            </div>

                            <div class="grid size-11 place-items-center rounded-lg ring-1 {{ $metric['accent'] }}">
                                <flux:icon :name="$metric['icon']" class="size-5" />
                            </div>
                        </div>

                        <p class="mt-4 border-t border-base-300/70 pt-3 text-sm text-base-content/60">{{ $metric['description'] }}</p>
                    </article>
                @endforeach
            </section>

            <x-card bodyClass="p-0">
                <div class="flex flex-col gap-3 border-b border-base-300/80 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                    <div>
                        <h2 class="text-lg font-semibold text-neutral">Permissoes de usuario</h2>
                        <p class="text-sm text-base-content/55">Altere roles operacionais sem conceder administrador.</p>
                    </div>

                    <div class="inline-flex items-center gap-2 rounded-md border border-base-300 bg-base-200/70 px-3 py-2 text-sm text-base-content/65">
                        <flux:icon.users class="size-4 text-primary" />
                        {{ $userItems->count() }} registro{{ $userItems->count() === 1 ? '' : 's' }}
                    </div>
                </div>

                @if ($users->isEmpty())
                    <div class="flex flex-col items-center gap-4 p-10 text-center">
                        <div class="grid size-12 place-items-center rounded-lg bg-primary/10 text-primary ring-1 ring-primary/15">
                            <flux:icon.users class="size-6" />
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-neutral">Nenhum usuario encontrado</h2>
                            <p class="mt-1 max-w-md text-sm text-base-content/65">
                                Quando usuarios forem cadastrados, eles aparecem aqui para controle de permissao.
                            </p>
                        </div>
                    </div>
                @else
                    <div class="grid gap-3 p-4 lg:hidden">
                        @foreach ($users as $user)
                            <div class="rounded-lg border border-base-300/80 bg-base-100 p-4 shadow-sm">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-neutral">{{ $user->name }}</p>
                                        <p class="truncate text-sm text-base-content/60">{{ $user->email }}</p>
                                        @if ($user->is(auth()->user()))
                                            <p class="mt-1 text-xs font-semibold text-primary">Seu usuario</p>
                                        @endif
                                    </div>
                                    <span class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $user->isAdmin() ? 'bg-secondary/10 text-secondary ring-secondary/20' : 'bg-base-200 text-base-content/70 ring-base-300' }}">
                                        <span class="size-1.5 rounded-full {{ $user->isAdmin() ? 'bg-secondary' : 'bg-base-content/35' }}"></span>
                                        {{ $user->role?->name ?? 'Sem permissao' }}
                                    </span>
                                </div>

                                <div class="mt-4">
                                    @if ($user->is(auth()->user()))
                                        <div class="rounded-md bg-base-200/70 p-3 text-sm text-base-content/60">Protegido para evitar auto-bloqueio.</div>
                                    @elseif ($user->isAdmin())
                                        <div class="rounded-md bg-base-200/70 p-3 text-sm text-base-content/60">Administrador protegido.</div>
                                    @else
                                        <form method="POST" action="{{ route('users.role.update', $user) }}" class="grid gap-2 sm:grid-cols-[minmax(0,1fr)_auto]">
                                            @csrf
                                            @method('PATCH')

                                            <select name="role_id" class="select select-bordered min-h-10 w-full bg-base-100">
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}" @selected((int) old('role_id', $user->role_id) === (int) $role->id)>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            <button type="submit" class="btn btn-primary btn-soft min-h-10 gap-2">
                                                <flux:icon.check class="size-4" />
                                                Salvar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="hidden overflow-x-auto lg:block">
                        <table class="min-w-full text-sm">
                            <thead class="bg-base-200/70 text-left text-xs font-semibold uppercase tracking-normal text-base-content/55">
                                <tr>
                                    <th class="px-5 py-4">Usuario</th>
                                    <th class="px-5 py-4">Email</th>
                                    <th class="px-5 py-4">Permissao atual</th>
                                    <th class="px-5 py-4">Alterar permissao</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-base-300/70">
                                @foreach ($users as $user)
                                    <tr class="transition hover:bg-primary/5">
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="grid size-10 place-items-center rounded-lg bg-primary/10 text-primary ring-1 ring-primary/15">
                                                    <flux:icon.user class="size-5" />
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-neutral">{{ $user->name }}</div>
                                                    @if ($user->is(auth()->user()))
                                                        <div class="text-xs font-semibold text-primary">Seu usuario</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-base-content/70">{{ $user->email }}</td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $user->isAdmin() ? 'bg-secondary/10 text-secondary ring-secondary/20' : 'bg-base-200 text-base-content/70 ring-base-300' }}">
                                                <span class="size-1.5 rounded-full {{ $user->isAdmin() ? 'bg-secondary' : 'bg-base-content/35' }}"></span>
                                                {{ $user->role?->name ?? 'Sem permissao' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4">
                                            @if ($user->is(auth()->user()))
                                                <div class="text-sm text-base-content/60">Protegido para evitar auto-bloqueio.</div>
                                            @elseif ($user->isAdmin())
                                                <div class="text-sm text-base-content/60">Administrador protegido.</div>
                                            @else
                                                <form method="POST" action="{{ route('users.role.update', $user) }}" class="flex max-w-xl flex-col gap-2 sm:flex-row">
                                                    @csrf
                                                    @method('PATCH')

                                                    <select name="role_id" class="select select-bordered min-h-10 w-full bg-base-100">
                                                        @foreach ($roles as $role)
                                                            <option value="{{ $role->id }}" @selected((int) old('role_id', $user->role_id) === (int) $role->id)>
                                                                {{ $role->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    <button type="submit" class="btn btn-primary btn-soft min-h-10 gap-2">
                                                        <flux:icon.check class="size-4" />
                                                        Salvar
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-card>

            <div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-layouts::app>
