<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\RestaurantTable;
use App\Models\TicketList;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TicketOpeningService
{
    private const OPEN_TICKET_STATUSES = ['aberta', 'em_andamento'];
    private const BRASILIA_TIMEZONE = 'America/Sao_Paulo';

    public function __construct(private readonly OperationalAudit $audit) {}

    public function availableTables(?CarbonInterface $at = null): Collection
    {
        $at = $this->brasilia($at);

        return RestaurantTable::query()
            ->where('status', RestaurantTable::STATUS_AVAILABLE)
            ->whereDoesntHave('tickets', $this->openTicketFilter())
            ->whereDoesntHave('reservations', $this->reservationConflictFilter($at))
            ->orderBy('identifier')
            ->get();
    }

    public function reservationsAvailableForOpening(?CarbonInterface $at = null): Collection
    {
        $at = $this->brasilia($at);

        return Reservation::query()
            ->with('restaurantTable')
            ->where('status', Reservation::STATUS_CONFIRMED)
            ->whereDoesntHave('ticket')
            ->where($this->reservationConflictFilter($at))
            ->orderBy('reserved_at')
            ->get();
    }

    public function openTicket(array $data, Collection $items): TicketList
    {
        return DB::transaction(function () use ($data, $items): TicketList {
            $reservation = $this->lockedReservation($data['reservation_id'] ?? null);
            $tableId = $reservation?->restaurant_table_id ?? ($data['restaurant_table_id'] ?? null);

            if (! $tableId) {
                throw ValidationException::withMessages([
                    'restaurant_table_id' => 'Selecione uma mesa livre ou uma reserva confirmada.',
                ]);
            }

            if ($reservation && filled($data['restaurant_table_id'] ?? null) && (int) $data['restaurant_table_id'] !== (int) $reservation->restaurant_table_id) {
                throw ValidationException::withMessages([
                    'reservation_id' => 'A reserva selecionada pertence a outra mesa.',
                ]);
            }

            $table = RestaurantTable::query()->lockForUpdate()->findOrFail($tableId);

            $this->ensureTableCanBeOpened($table, $reservation);

            $ticket = TicketList::query()->create([
                'customer_name' => $data['customer_name'] ?? $reservation?->customer_name,
                'table_number' => $table->identifier,
                'restaurant_table_id' => $table->id,
                'reservation_id' => $reservation?->id,
                'status' => 'aberta',
                'priority' => $data['priority'],
                'total_amount' => $items->sum('subtotal'),
                'notes' => $data['notes'] ?? null,
            ]);

            $ticket->items()->createMany($items->all());

            $table->forceFill([
                'status' => RestaurantTable::STATUS_OCCUPIED,
            ])->save();

            $reservation?->forceFill([
                'status' => Reservation::STATUS_COMPLETED,
            ])->save();

            $this->audit->record('ticket.opened', $ticket, [
                'restaurant_table_id' => $table->id,
                'reservation_id' => $reservation?->id,
            ]);

            return $ticket->load(['restaurantTable', 'reservation', 'items']);
        });
    }

    private function lockedReservation(null|int|string $reservationId): ?Reservation
    {
        if (! filled($reservationId)) {
            return null;
        }

        $reservation = Reservation::query()
            ->with('ticket')
            ->lockForUpdate()
            ->findOrFail($reservationId);

        if ($reservation->status !== Reservation::STATUS_CONFIRMED) {
            throw ValidationException::withMessages([
                'reservation_id' => 'A reserva precisa estar confirmada para abrir uma comanda.',
            ]);
        }

        if ($reservation->ticket !== null) {
            throw ValidationException::withMessages([
                'reservation_id' => 'Esta reserva ja possui uma comanda vinculada.',
            ]);
        }

        if (! $this->reservationConflictsAt($reservation, now(self::BRASILIA_TIMEZONE))) {
            throw ValidationException::withMessages([
                'reservation_id' => 'A reserva selecionada nao esta dentro do horario de atendimento atual.',
            ]);
        }

        return $reservation;
    }

    private function ensureTableCanBeOpened(RestaurantTable $table, ?Reservation $reservation): void
    {
        $allowedStatuses = $reservation
            ? [RestaurantTable::STATUS_AVAILABLE, RestaurantTable::STATUS_RESERVED]
            : [RestaurantTable::STATUS_AVAILABLE];

        if (! in_array($table->status, $allowedStatuses, true)) {
            throw ValidationException::withMessages([
                'restaurant_table_id' => 'A mesa selecionada nao esta disponivel para abertura de comanda.',
            ]);
        }

        if ($table->tickets()->whereIn('status', self::OPEN_TICKET_STATUSES)->exists()) {
            throw ValidationException::withMessages([
                'restaurant_table_id' => 'A mesa selecionada ja possui uma comanda aberta.',
            ]);
        }

        if (! $reservation && $table->reservations()->where($this->reservationConflictFilter(now(self::BRASILIA_TIMEZONE)))->exists()) {
            throw ValidationException::withMessages([
                'restaurant_table_id' => 'A mesa selecionada possui uma reserva confirmada para este horario.',
            ]);
        }
    }

    private function reservationConflictsAt(Reservation $reservation, CarbonInterface $at): bool
    {
        return $reservation->reserved_at->lessThanOrEqualTo($at)
            && $reservation->reserved_at->copy()->addMinutes($reservation->duration_minutes)->greaterThan($at);
    }

    private function openTicketFilter(): callable
    {
        return fn ($query) => $query->whereIn('status', self::OPEN_TICKET_STATUSES);
    }

    private function reservationConflictFilter(CarbonInterface $at): callable
    {
        $at = $this->brasilia($at);

        return fn ($query) => $query
            ->where('status', Reservation::STATUS_CONFIRMED)
            ->where('reserved_at', '<=', $at)
            ->whereRaw($this->reservationEndsAfterSql(), [$at]);
    }

    private function reservationEndsAfterSql(): string
    {
        return DB::connection()->getDriverName() === 'sqlite'
            ? "datetime(reserved_at, '+' || duration_minutes || ' minutes') > ?"
            : 'DATE_ADD(reserved_at, INTERVAL duration_minutes MINUTE) > ?';
    }

    private function brasilia(?CarbonInterface $at = null): CarbonInterface
    {
        return $at
            ? $at->copy()->timezone(self::BRASILIA_TIMEZONE)
            : now(self::BRASILIA_TIMEZONE);
    }
}
