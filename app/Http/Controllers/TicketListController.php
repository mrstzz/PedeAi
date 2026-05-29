<?php

namespace App\Http\Controllers;

use App\Models\TicketList;
use App\Models\MenuItem;
use App\Models\TicketItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TicketListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('ticket-list.index', [
            'tickets' => TicketList::query()
                ->withCount('items')
                ->latest('opened_at')
                ->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ticket-list.create', [
            'menuItems' => MenuItem::query()
                ->where('active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:255'],
            'table_number' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['aberta', 'em_andamento', 'fechada', 'paga', 'cancelada'])],
            'priority' => ['required', Rule::in(['normal', 'alta'])],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_item_id' => ['nullable', 'integer', Rule::exists('menu_items', 'id')->where('active', true)],
            'items.*.quantity' => ['required_with:items.*.menu_item_id', 'nullable', 'integer', 'min:1'],
            'items.*.notes' => ['nullable', 'string'],
        ]);

        $items = $this->buildItems($data['items']);

        if ($items->isEmpty()) {
            return back()
                ->withErrors(['items' => 'Informe ao menos um item para a comanda.'])
                ->withInput();
        }

        DB::transaction(function () use ($data, $items): void {
            $ticket = TicketList::query()->create([
                'customer_name' => $data['customer_name'] ?? null,
                'table_number' => $data['table_number'] ?? null,
                'status' => $data['status'],
                'priority' => $data['priority'],
                'total_amount' => $items->sum('subtotal'),
                'notes' => $data['notes'] ?? null,
                'closed_at' => in_array($data['status'], ['fechada', 'paga', 'cancelada'], true) ? now() : null,
            ]);

            $ticket->items()->createMany($items->all());
        });

        return redirect()
            ->route('ticket-list.index')
            ->with('status', 'Comanda criada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TicketList $ticketList)
    {
        return view('ticket-list.show', [
            'ticket' => $ticketList->load('items'),
            'menuItems' => MenuItem::query()
                ->where('active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TicketList $ticketList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TicketList $ticketList)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TicketList $ticketList)
    {
        //
    }

    public function storeItems(Request $request, TicketList $ticketList)
    {
        abort_if(in_array($ticketList->status, ['fechada', 'paga', 'cancelada'], true), 422, 'Nao e possivel adicionar itens em uma comanda encerrada.');

        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_item_id' => ['nullable', 'integer', Rule::exists('menu_items', 'id')->where('active', true)],
            'items.*.quantity' => ['required_with:items.*.menu_item_id', 'nullable', 'integer', 'min:1'],
            'items.*.notes' => ['nullable', 'string'],
        ]);

        $items = $this->buildItems($data['items']);

        if ($items->isEmpty()) {
            return back()
                ->withErrors(['items' => 'Informe ao menos um item para adicionar.'])
                ->withInput();
        }

        DB::transaction(function () use ($ticketList, $items): void {
            $ticketList->items()->createMany($items->all());
            $ticketList->recalculateTotal();
        });

        return redirect()
            ->route('ticket-list.show', $ticketList)
            ->with('status', 'Itens adicionados com sucesso.');
    }

    public function startPreparation(TicketList $ticketList)
    {
        abort_unless(auth()->user()?->canAccessKitchenQueue(), 403);
        abort_if(in_array($ticketList->status, ['fechada', 'paga', 'cancelada'], true), 422);

        $ticketList->forceFill(['status' => 'em_andamento'])->save();

        $ticketList->items()
            ->where('status', 'pendente')
            ->update(['status' => 'em_preparo']);

        return back()->with('status', 'Comanda enviada para preparo.');
    }

    public function deliverItem(TicketItem $ticketItem)
    {
        abort_unless(auth()->user()?->canAccessKitchenQueue(), 403);

        $ticketItem->forceFill([
            'status' => 'entregue',
            'delivered_at' => now(),
        ])->save();

        return back()->with('status', 'Item marcado como entregue.');
    }

    private function buildItems(array $rawItems)
    {
        $menuItems = MenuItem::query()
            ->where('active', true)
            ->whereIn('id', collect($rawItems)->pluck('menu_item_id')->filter())
            ->get()
            ->keyBy('id');

        return collect($rawItems)
            ->filter(fn (array $item): bool => filled($item['menu_item_id'] ?? null))
            ->map(function (array $item) use ($menuItems): array {
                $menuItem = $menuItems->get((int) $item['menu_item_id']);
                $quantity = max(1, (int) ($item['quantity'] ?? 1));
                $unitPrice = (float) $menuItem->price;

                return [
                    'product_name' => $menuItem->name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $quantity * $unitPrice,
                    'status' => 'pendente',
                    'notes' => $item['notes'] ?? null,
                ];
            })
            ->values();
    }
}
