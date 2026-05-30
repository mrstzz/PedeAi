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

        abort_if($user->is($request->user()), 403, 'Nao e possivel alterar a sua propria permissão.');

        $assignableRoleIds = $this->assignableRoles()->pluck('id')->all();

        $data = $request->validate([
            'role_id' => ['required', 'integer', Rule::in($assignableRoleIds)],
        ], [
            'role_id.required' => 'Selecione uma role para o usuario.',
            'role_id.in' => 'Esta role nao pode ser atribuida por aqui.',
        ]);

        $role = Role::query()->findOrFail($data['role_id']);

        abort_if($role->isAdmin(), 403, 'Nao e permitido atribuir administrador para outro usuario.');

        $user->forceFill([
            'role_id' => $role->id,
        ])->save();

        return back()->with('status', 'Permissão do usuario atualizada com sucesso.');
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
