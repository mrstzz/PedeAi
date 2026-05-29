<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        return view('menu-items.index', [
            'menuItems' => MenuItem::query()
                ->latest()
                ->paginate(10),
        ]);
    }

    public function create()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        return view('menu-items.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'active' => ['nullable', 'boolean'],
        ]);

        MenuItem::query()->create([
            'name' => $data['name'],
            'price' => $data['price'],
            'description' => $data['description'] ?? null,
            'active' => (bool) ($data['active'] ?? false),
        ]);

        return redirect()
            ->route('menu-items.index')
            ->with('status', 'Item cadastrado com sucesso.');
    }
}
