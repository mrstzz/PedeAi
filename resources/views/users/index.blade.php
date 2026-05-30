<x-layouts::app :title="__('Usuarios')">
    <div class="min-h-full bg-base-200 text-base-content">
        <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8">
            <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="badge badge-secondary badge-outline mb-3">Administracao</div>
                    <h1 class="text-2xl font-bold text-neutral sm:text-3xl">Usuarios</h1>
                    <p class="mt-2 max-w-2xl text-sm text-base-content/70">
                        Gerencie as permissões dos usuarios. A permissão de administrador nao pode ser atribuida por esta tela.
                    </p>
                </div>
            </section>

            @if (session('status'))
                <div class="alert alert-success">
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    <div>
                        <h2 class="font-semibold">Revise os dados informados</h2>
                        <p class="text-sm">{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            <x-card bodyClass="p-0">
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Permissão Atual</th>
                                <th class="w-80">Alterar Permissão</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>
                                        <div class="font-semibold text-neutral">{{ $user->name }}</div>
                                        @if ($user->is(auth()->user()))
                                            <div class="text-xs text-red-400 font-bold text-base-content/55">(seu usuário)</div>
                                        @endif
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge {{ $user->isAdmin() ? 'badge-primary' : 'badge-ghost' }}">
                                            {{ $user->role?->name ?? 'Sem permissão' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($user->is(auth()->user()))
                                            <div class="text-sm text-base-content/60">Protegido para evitar auto-bloqueio.</div>
                                        @elseif ($user->isAdmin())
                                            <div class="text-sm text-base-content/60">Administrador protegido.</div>
                                        @else
                                            <form method="POST" action="{{ route('users.role.update', $user) }}" class="flex flex-col gap-2 sm:flex-row">
                                                @csrf
                                                @method('PATCH')

                                                <select name="role_id" class="select select-bordered min-h-10 w-full bg-base-100">
                                                    @foreach ($roles as $role)
                                                        <option value="{{ $role->id }}" @selected((int) old('role_id', $user->role_id) === (int) $role->id)>
                                                            {{ $role->name }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <button type="submit" class="btn btn-primary btn-soft min-h-10">
                                                    Salvar
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <div class="p-8 text-center text-sm text-base-content/65">
                                            Nenhum usuario encontrado.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            {{ $users->links() }}
        </div>
    </div>
</x-layouts::app>
