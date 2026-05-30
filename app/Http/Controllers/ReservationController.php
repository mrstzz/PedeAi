<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class ReservationController extends Controller
{
    private const BRASILIA_TIMEZONE = 'America/Sao_Paulo';

    public function index()
    {
        return view('reservations.index', [
            'reservations' => Reservation::query()
                ->with(['restaurantTable', 'ticket'])
                ->latest('reserved_at')
                ->paginate(12),
        ]);
    }

    public function create(ReservationService $reservationService)
    {
        return view('reservations.create', [
            'tables' => $reservationService->availableTablesForReservation(),
        ]);
    }

    public function store(Request $request, ReservationService $reservationService)
    {
        $data = $request->validate([
            'restaurant_table_id' => ['required', 'integer', Rule::exists('restaurant_tables', 'id')],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'reserved_at' => ['required', 'date'],
            'duration_minutes' => ['required', 'integer', 'min:30', 'max:480'],
            'party_size' => ['nullable', 'integer', 'min:1', 'max:100'],
            'notes' => ['nullable', 'string'],
        ], [
            'restaurant_table_id.required' => 'Selecione uma mesa para a reserva.',
            'customer_name.required' => 'Informe o nome do cliente.',
            'reserved_at.required' => 'Informe a data e hora da reserva.',
            'reserved_at.after_or_equal' => 'A reserva precisa ser para agora ou para um horario futuro.',
            'duration_minutes.required' => 'Informe a duracao da reserva.',
            'duration_minutes.min' => 'A reserva precisa ter pelo menos 30 minutos.',
        ]);

        $data['reserved_at'] = Carbon::parse($data['reserved_at'], self::BRASILIA_TIMEZONE);

        if ($data['reserved_at']->lessThan(now(self::BRASILIA_TIMEZONE))) {
            return back()
                ->withErrors(['reserved_at' => 'A reserva precisa ser para agora ou para um horario futuro no horario de Brasilia.'])
                ->withInput();
        }

        $reservationService->create($data);

        return redirect()
            ->route('reservations.index')
            ->with('status', 'Reserva cadastrada com sucesso.');
    }

    public function cancel(Reservation $reservation, ReservationService $reservationService)
    {
        $reservationService->cancel($reservation);

        return back()->with('status', 'Reserva cancelada com sucesso.');
    }
}
