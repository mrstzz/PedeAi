<?php

namespace App\Http\Controllers;

use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RestaurantTableController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();

        return view('restaurant-tables.index', [
            'tables' => RestaurantTable::query()
                ->withCount([
                    'tickets as open_tickets_count' => fn ($query) => $query->whereIn('status', ['aberta', 'em_andamento']),
                    'reservations as active_reservations_count' => fn ($query) => $query->where('status', 'confirmada'),
                ])
                ->orderByRaw('identifier + 0 asc')
                ->orderBy('identifier')
                ->paginate(12),
        ]);
    }

    public function create()
    {
        $this->authorizeAdmin();

        return view('restaurant-tables.create', [
            'statuses' => $this->statuses(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'identifier' => ['required', 'string', 'max:50', Rule::unique('restaurant_tables', 'identifier')],
            'capacity' => ['required', 'integer', 'min:1', 'max:100'],
            'status' => ['required', Rule::in(array_keys($this->statuses()))],
        ], [
            'identifier.required' => 'Informe a identificacao da mesa.',
            'identifier.unique' => 'Ja existe uma mesa com esta identificacao.',
            'capacity.required' => 'Informe a capacidade da mesa.',
            'capacity.integer' => 'A capacidade precisa ser um numero inteiro.',
            'capacity.min' => 'A capacidade precisa ser pelo menos 1.',
            'status.required' => 'Selecione o status da mesa.',
            'status.in' => 'Selecione um status valido para a mesa.',
        ]);

        RestaurantTable::query()->create($data);

        return redirect()
            ->route('restaurant-tables.index')
            ->with('status', 'Mesa cadastrada com sucesso.');
    }

    public function edit(RestaurantTable $restaurantTable)
    {
        $this->authorizeAdmin();

        return view('restaurant-tables.edit', [
            'table' => $restaurantTable,
            'statuses' => $this->statuses(),
        ]);
    }

    public function update(Request $request, RestaurantTable $restaurantTable)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'identifier' => ['required', 'string', 'max:50', Rule::unique('restaurant_tables', 'identifier')->ignore($restaurantTable)],
            'capacity' => ['required', 'integer', 'min:1', 'max:100'],
            'status' => ['required', Rule::in(array_keys($this->statuses()))],
        ]);

        $restaurantTable->update($data);

        return redirect()
            ->route('restaurant-tables.index')
            ->with('status', 'Mesa atualizada com sucesso.');
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    private function statuses(): array
    {
        return [
            RestaurantTable::STATUS_AVAILABLE => 'Disponivel',
            RestaurantTable::STATUS_OCCUPIED => 'Ocupada',
            RestaurantTable::STATUS_RESERVED => 'Reservada',
            RestaurantTable::STATUS_MAINTENANCE => 'Manutencao',
        ];
    }
}
