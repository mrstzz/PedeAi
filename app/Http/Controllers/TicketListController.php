<?php

namespace App\Http\Controllers;

use App\Models\TicketList;
use App\Models\MenuItem;
use App\Models\TicketItem;
use App\Models\RestaurantTable;
use App\Services\TicketOpeningService;
use App\Services\TicketPaymentService;
use App\Services\OperationalAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TicketListController extends Controller
{
    private const BRASILIA_TIMEZONE = 'America/Sao_Paulo';

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
    public function create(TicketOpeningService $ticketOpeningService)
    {
        return view('ticket-list.create', [
            'menuItems' => MenuItem::query()
                ->where('active', true)
                ->orderBy('name')
                ->get(),
            'availableTables' => $ticketOpeningService->availableTables(),
            'activeReservations' => $ticketOpeningService->reservationsAvailableForOpening(),
        ]);
    }

    public function availableTables(Request $request, TicketOpeningService $ticketOpeningService)
    {
        $data = $request->validate([
            'at' => ['nullable', 'date'],
        ]);

        $at = filled($data['at'] ?? null)
            ? \Illuminate\Support\Carbon::parse($data['at'], self::BRASILIA_TIMEZONE)
            : now(self::BRASILIA_TIMEZONE);

        return response()->json([
            'data' => $ticketOpeningService->availableTables($at)->map(fn ($table): array => [
                'id' => $table->id,
                'identifier' => $table->identifier,
                'capacity' => $table->capacity,
                'status' => $table->status,
            ])->values(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, TicketOpeningService $ticketOpeningService)
    {
        $data = $request->validate(
            $this->ticketRules(),
            $this->ticketMessages(),
            $this->ticketAttributes(),
        );

        $items = $this->buildItems($data['items']);

        if ($items->isEmpty()) {
            return back()
                ->withErrors(['items' => 'Informe ao menos um item para a comanda.'])
                ->withInput();
        }

        $ticketOpeningService->openTicket($data, $items);

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
            'ticket' => $ticketList->load(['items', 'restaurantTable', 'reservation', 'events.user']),
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
        ], $this->ticketMessages(), $this->ticketAttributes());

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

    public function updateStatus(Request $request, TicketList $ticketList, OperationalAudit $audit, TicketPaymentService $paymentService)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['aberta', 'em_andamento', 'fechada', 'paga', 'cancelada'])],
        ], [
            'status.required' => 'Selecione um status para a comanda.',
            'status.in' => 'Selecione um status valido para a comanda.',
        ]);

        $isClosedStatus = in_array($data['status'], ['fechada', 'paga', 'cancelada'], true);
        $releasesTable = in_array($data['status'], ['paga', 'cancelada'], true);

        DB::transaction(function () use ($ticketList, $data, $isClosedStatus, $releasesTable, $audit, $paymentService): void {
            $previousStatus = $ticketList->status;

            $ticketList->forceFill([
                'status' => $data['status'],
                'closed_at' => $isClosedStatus ? ($ticketList->closed_at ?? now()) : null,
            ])->save();

            if ($releasesTable) {
                $paymentService->releaseTable($ticketList);
            } elseif ($ticketList->restaurant_table_id) {
                $table = $ticketList->restaurantTable()->lockForUpdate()->first();

                if ($table) {
                    $table->forceFill([
                        'status' => RestaurantTable::STATUS_OCCUPIED,
                    ])->save();
                }
            }

            $audit->record('ticket.status_changed', $ticketList, [
                'from' => $previousStatus,
                'to' => $data['status'],
            ]);
        });

        return back()->with('status', 'Status da comanda atualizado com sucesso.');
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

    public function pay(Request $request, TicketList $ticketList, TicketPaymentService $paymentService)
    {
        $data = $request->validate([
            'payment_method' => ['required', Rule::in(['dinheiro', 'pix', 'debito', 'credito', 'outro'])],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'service_amount' => ['nullable', 'numeric', 'min:0'],
        ], [
            'payment_method.required' => 'Selecione a forma de pagamento.',
            'payment_method.in' => 'Selecione uma forma de pagamento valida.',
        ]);

        $paymentService->pay($ticketList, $data);

        return back()->with('status', 'Comanda paga com sucesso.');
    }

    private function ticketRules(): array
    {
        return [
            'customer_name' => ['nullable', 'string', 'max:255'],
            'restaurant_table_id' => ['nullable', 'required_without:reservation_id', 'integer', Rule::exists('restaurant_tables', 'id')],
            'reservation_id' => ['nullable', 'integer', Rule::exists('reservations', 'id')],
            'status' => ['nullable', Rule::in(['aberta', 'em_andamento', 'fechada', 'paga', 'cancelada'])],
            'priority' => ['required', Rule::in(['normal', 'alta'])],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_item_id' => ['nullable', 'integer', Rule::exists('menu_items', 'id')->where('active', true)],
            'items.*.quantity' => ['required_with:items.*.menu_item_id', 'nullable', 'integer', 'min:1'],
            'items.*.notes' => ['nullable', 'string'],
        ];
    }

    private function ticketMessages(): array
    {
        return [
            'items.required' => 'Informe ao menos um item para a comanda.',
            'items.min' => 'Informe ao menos um item para a comanda.',
            'items.*.menu_item_id.exists' => 'Selecione um item ativo do cardapio.',
            'items.*.quantity.required_with' => 'Informe a quantidade do item selecionado.',
            'items.*.quantity.integer' => 'A quantidade precisa ser um numero inteiro.',
            'items.*.quantity.min' => 'A quantidade precisa ser pelo menos 1.',
            'restaurant_table_id.required_without' => 'Selecione uma mesa livre ou uma reserva confirmada.',
            'restaurant_table_id.exists' => 'Selecione uma mesa cadastrada.',
            'reservation_id.exists' => 'Selecione uma reserva cadastrada.',
        ];
    }

    private function ticketAttributes(): array
    {
        return [
            'customer_name' => 'cliente',
            'restaurant_table_id' => 'mesa',
            'reservation_id' => 'reserva',
            'items.*.menu_item_id' => 'item do cardapio',
            'items.*.quantity' => 'quantidade',
            'items.*.notes' => 'observacoes do item',
        ];
    }
}
