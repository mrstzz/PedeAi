<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();

        return view('users.index', [
            'users' => User::query()
                ->with('role')
                ->orderBy('name')
                ->paginate(12),
            'roles' => $this->assignableRoles(),
        ]);
    }

    public function updateRole(Request $request, User $user)
    {
        $this->authorizeAdmin();

        abort_if($user->is($request->user()), 403, 'Não é possível alterar a sua própria permissão.');

        $assignableRoleIds = $this->assignableRoles()->pluck('id')->all();

        $data = $request->validate([
            'role_id' => ['required', 'integer', Rule::in($assignableRoleIds)],
        ], [
            'role_id.required' => 'Selecione uma permissão para o usuário.',
            'role_id.in' => 'Esta permissão não pode ser atribuída por aqui.',
        ]);

        $role = Role::query()->findOrFail($data['role_id']);

        abort_if($role->isAdmin(), 403, 'Não é permitido atribuir administrador para outro usuário.');

        $user->forceFill([
            'role_id' => $role->id,
        ])->save();

        return back()->with('status', 'Permissão do usuário atualizada com sucesso.');
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    private function assignableRoles()
    {
        return Role::query()
            ->where('id', '!=', Role::ADMIN_ID)
            ->whereNotIn('slug', ['administrador', 'admin'])
            ->orderBy('name')
            ->get();
    }
}
