<?php

namespace App\Services;

use App\Models\RestaurantTable;
use App\Models\TicketList;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TicketPaymentService
{
    public function __construct(private readonly OperationalAudit $audit) {}

    public function pay(TicketList $ticket, array $data): TicketList
    {
        return DB::transaction(function () use ($ticket, $data): TicketList {
            $ticket = TicketList::query()->with('restaurantTable')->lockForUpdate()->findOrFail($ticket->id);

            if (in_array($ticket->status, ['paga', 'cancelada'], true)) {
                throw ValidationException::withMessages([
                    'status' => 'Esta comanda ja foi paga ou cancelada.',
                ]);
            }

            $discount = (float) ($data['discount_amount'] ?? 0);
            $service = (float) ($data['service_amount'] ?? 0);
            $paid = max(0, ((float) $ticket->total_amount + $service) - $discount);

            $ticket->forceFill([
                'status' => 'paga',
                'discount_amount' => $discount,
                'service_amount' => $service,
                'paid_amount' => $paid,
                'payment_method' => $data['payment_method'],
                'closed_at' => $ticket->closed_at ?? now(),
                'paid_at' => now(),
            ])->save();

            $this->releaseTable($ticket);

            $this->audit->record('ticket.paid', $ticket, [
                'payment_method' => $ticket->payment_method,
                'paid_amount' => $ticket->paid_amount,
                'discount_amount' => $ticket->discount_amount,
                'service_amount' => $ticket->service_amount,
            ]);

            return $ticket;
        });
    }

    public function releaseTable(TicketList $ticket): void
    {
        if (! $ticket->restaurant_table_id) {
            return;
        }

        $table = $ticket->restaurantTable()->lockForUpdate()->first();

        if (! $table) {
            return;
        }

        $hasBlockingTicket = $table->tickets()
            ->whereKeyNot($ticket->id)
            ->whereIn('status', ['aberta', 'em_andamento', 'fechada'])
            ->exists();

        if (! $hasBlockingTicket) {
            $table->forceFill([
                'status' => RestaurantTable::STATUS_AVAILABLE,
            ])->save();
        }
    }
}
