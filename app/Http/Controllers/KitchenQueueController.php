<?php

namespace App\Http\Controllers;

use App\Models\TicketList;

class KitchenQueueController extends Controller
{
    public function __invoke()
    {
        abort_unless(auth()->user()?->canAccessKitchenQueue(), 403);

        return view('kitchen-queue.index', [
            'tickets' => TicketList::query()
                ->with('items')
                ->whereIn('status', ['aberta', 'em_andamento'])
                ->orderByRaw("case when priority = 'alta' then 0 else 1 end")
                ->oldest('opened_at')
                ->get(),
        ]);
    }
}
