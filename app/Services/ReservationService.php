<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\RestaurantTable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservationService
{
    private const OPEN_TICKET_STATUSES = ['aberta', 'em_andamento', 'fechada'];
    private const BRASILIA_TIMEZONE = 'America/Sao_Paulo';

    public function availableTablesForReservation(): Collection
    {
        return RestaurantTable::query()
            ->where('status', RestaurantTable::STATUS_AVAILABLE)
            ->whereDoesntHave('tickets', fn ($query) => $query->whereIn('status', self::OPEN_TICKET_STATUSES))
            ->orderBy('identifier')
            ->get();
    }

    public function create(array $data): Reservation
    {
        return DB::transaction(function () use ($data): Reservation {
            $reservedAt = $data['reserved_at']->copy()->timezone(self::BRASILIA_TIMEZONE);
            $durationMinutes = (int) $data['duration_minutes'];
            $endsAt = $reservedAt->copy()->addMinutes($durationMinutes);

            $table = RestaurantTable::query()
                ->lockForUpdate()
                ->findOrFail($data['restaurant_table_id']);

            if ($table->status !== RestaurantTable::STATUS_AVAILABLE) {
                throw ValidationException::withMessages([
                    'restaurant_table_id' => 'A mesa selecionada nao esta disponivel para reserva.',
                ]);
            }

            if ($table->tickets()->whereIn('status', self::OPEN_TICKET_STATUSES)->exists()) {
                throw ValidationException::withMessages([
                    'restaurant_table_id' => 'A mesa selecionada possui uma comanda em aberto.',
                ]);
            }

            if ($this->hasReservationConflict($table, $reservedAt, $endsAt)) {
                throw ValidationException::withMessages([
                    'restaurant_table_id' => 'A mesa selecionada ja possui reserva nesse horario.',
                ]);
            }

            $reservation = Reservation::query()->create([
                'restaurant_table_id' => $table->id,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'reserved_at' => $reservedAt,
                'duration_minutes' => $durationMinutes,
                'party_size' => $data['party_size'] ?? null,
                'status' => Reservation::STATUS_CONFIRMED,
                'notes' => $data['notes'] ?? null,
            ]);

            $table->forceFill([
                'status' => RestaurantTable::STATUS_RESERVED,
            ])->save();

            return $reservation->load('restaurantTable');
        });
    }

    public function cancel(Reservation $reservation): void
    {
        DB::transaction(function () use ($reservation): void {
            $reservation = Reservation::query()
                ->with('ticket')
                ->lockForUpdate()
                ->findOrFail($reservation->id);

            if ($reservation->ticket && in_array($reservation->ticket->status, self::OPEN_TICKET_STATUSES, true)) {
                throw ValidationException::withMessages([
                    'reservation' => 'Nao e possivel cancelar uma reserva com comanda ainda aberta.',
                ]);
            }

            $reservation->forceFill([
                'status' => Reservation::STATUS_CANCELED,
            ])->save();

            $table = $reservation->restaurantTable()
                ->lockForUpdate()
                ->first();

            if (! $table) {
                return;
            }

            $hasOpenTicket = $table->tickets()
                ->whereIn('status', self::OPEN_TICKET_STATUSES)
                ->exists();

            $hasOtherActiveReservation = $table->reservations()
                ->whereKeyNot($reservation->id)
                ->whereIn('status', [Reservation::STATUS_PENDING, Reservation::STATUS_CONFIRMED])
                ->exists();

            if (! $hasOpenTicket && ! $hasOtherActiveReservation) {
                $table->forceFill([
                    'status' => RestaurantTable::STATUS_AVAILABLE,
                ])->save();
            }
        });
    }

    private function hasReservationConflict(RestaurantTable $table, CarbonInterface $startsAt, CarbonInterface $endsAt): bool
    {
        $startsAt = $startsAt->copy()->timezone(self::BRASILIA_TIMEZONE);
        $endsAt = $endsAt->copy()->timezone(self::BRASILIA_TIMEZONE);

        return $table->reservations()
            ->whereIn('status', [Reservation::STATUS_PENDING, Reservation::STATUS_CONFIRMED])
            ->where('reserved_at', '<', $endsAt)
            ->whereRaw('DATE_ADD(reserved_at, INTERVAL duration_minutes MINUTE) > ?', [$startsAt])
            ->exists();
    }
}
