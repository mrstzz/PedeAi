<?php

namespace App\Http\Controllers;

use App\Models\TicketList;
use App\Models\Reservation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->user()?->isWaiter()) {
            return view('waiter.dashboard', [
                'openTicketsCount' => TicketList::query()
                    ->whereIn('status', ['aberta', 'em_andamento', 'fechada'])
                    ->count(),
                'reservationsCount' => Reservation::query()
                    ->whereIn('status', ['pendente', 'confirmada'])
                    ->count(),
                'recentTickets' => TicketList::query()
                    ->latest('opened_at')
                    ->take(4)
                    ->get(),
            ]);
        }

        $search = trim((string) $request->query('search'));
        $status = $request->query('status', 'todos');
        $statuses = ['aberta', 'em_andamento', 'fechada', 'paga', 'cancelada'];

        $ticketQuery = TicketList::query()
            ->withCount('items')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('customer_name', 'like', "%{$search}%")
                        ->orWhere('table_number', 'like', "%{$search}%")
                        ->when(is_numeric($search), fn ($query) => $query->orWhere('id', $search));
                });
            })
            ->when(in_array($status, $statuses, true), fn ($query) => $query->where('status', $status))
            ->latest('opened_at');

        $tickets = $ticketQuery->take(12)->get();

        $statusCounts = TicketList::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalTickets = $statusCounts->sum();

        $metrics = [
            'open' => (int) $statusCounts->get('aberta', 0),
            'inProgress' => (int) $statusCounts->get('em_andamento', 0),
            'closed' => (int) $statusCounts->get('fechada', 0),
            'paid' => (int) $statusCounts->get('paga', 0),
            'canceled' => (int) $statusCounts->get('cancelada', 0),
            'openAmount' => TicketList::query()->where('status', 'aberta')->sum('total_amount'),
            'revenueToday' => TicketList::query()
                ->where('status', 'paga')
                ->whereDate('updated_at', now()->toDateString())
                ->sum('total_amount'),
        ];

        $recentTickets = TicketList::query()
            ->latest('updated_at')
            ->take(5)
            ->get();

        return view('dashboard', [
            'tickets' => $tickets,
            'metrics' => $metrics,
            'statusCounts' => $statusCounts,
            'statusPercentages' => collect($statuses)->mapWithKeys(fn ($status) => [
                $status => $totalTickets > 0 ? round(((int) $statusCounts->get($status, 0) / $totalTickets) * 100) : 0,
            ]),
            'recentTickets' => $recentTickets,
            'selectedStatus' => $status,
            'search' => $search,
        ]);
    }
}
