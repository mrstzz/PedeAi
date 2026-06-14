<?php

use App\Models\MenuItem;
use App\Models\Reservation;
use App\Models\RestaurantTable;
use App\Models\Role;
use App\Models\TicketList;
use App\Models\User;
use App\Services\ReservationService;
use App\Services\TicketOpeningService;
use App\Services\TicketPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

function createRole(string $name, string $slug): Role
{
    return Role::query()->firstOrCreate(
        ['slug' => $slug],
        ['name' => $name],
    );
}

function createUserWithRole(string $slug): User
{
    $role = Role::query()->firstOrCreate(
        ['slug' => $slug],
        ['name' => ucfirst($slug)],
    );

    return User::factory()->create([
        'role_id' => $role->id,
    ]);
}

function createMenuItem(): MenuItem
{
    return MenuItem::query()->create([
        'name' => 'Café',
        'price' => 6,
        'active' => true,
    ]);
}

it('does not list tables with active reservations as available', function () {
    $table = RestaurantTable::query()->create([
        'identifier' => '1',
        'capacity' => 4,
        'status' => RestaurantTable::STATUS_AVAILABLE,
    ]);

    Reservation::query()->create([
        'restaurant_table_id' => $table->id,
        'customer_name' => 'Cliente',
        'reserved_at' => now('America/Sao_Paulo')->subMinutes(10),
        'duration_minutes' => 120,
        'status' => Reservation::STATUS_CONFIRMED,
    ]);

    expect(app(TicketOpeningService::class)->availableTables())->toHaveCount(0);
});

it('opens a ticket linked to a reservation and marks table occupied', function () {
    $menuItem = createMenuItem();
    $table = RestaurantTable::query()->create([
        'identifier' => '2',
        'capacity' => 4,
        'status' => RestaurantTable::STATUS_RESERVED,
    ]);
    $reservation = Reservation::query()->create([
        'restaurant_table_id' => $table->id,
        'customer_name' => 'Maria',
        'reserved_at' => now('America/Sao_Paulo')->subMinutes(5),
        'duration_minutes' => 120,
        'status' => Reservation::STATUS_CONFIRMED,
    ]);

    $items = collect([[
        'product_name' => $menuItem->name,
        'quantity' => 1,
        'unit_price' => 6,
        'subtotal' => 6,
        'status' => 'pendente',
        'notes' => null,
    ]]);

    $ticket = app(TicketOpeningService::class)->openTicket([
        'reservation_id' => $reservation->id,
        'priority' => 'normal',
    ], $items);

    expect($ticket->reservation_id)->toBe($reservation->id)
        ->and($ticket->restaurant_table_id)->toBe($table->id)
        ->and($table->fresh()->status)->toBe(RestaurantTable::STATUS_OCCUPIED)
        ->and($reservation->fresh()->status)->toBe(Reservation::STATUS_COMPLETED);
});

it('blocks opening a normal ticket on a reserved table', function () {
    createMenuItem();
    $table = RestaurantTable::query()->create([
        'identifier' => '3',
        'capacity' => 4,
        'status' => RestaurantTable::STATUS_RESERVED,
    ]);

    app(TicketOpeningService::class)->openTicket([
        'restaurant_table_id' => $table->id,
        'priority' => 'normal',
    ], collect([[
        'product_name' => 'Café',
        'quantity' => 1,
        'unit_price' => 6,
        'subtotal' => 6,
        'status' => 'pendente',
        'notes' => null,
    ]]));
})->throws(ValidationException::class);

it('releases the table when a ticket is paid', function () {
    $table = RestaurantTable::query()->create([
        'identifier' => '4',
        'capacity' => 4,
        'status' => RestaurantTable::STATUS_OCCUPIED,
    ]);
    $ticket = TicketList::query()->create([
        'customer_name' => 'Cliente',
        'table_number' => '4',
        'restaurant_table_id' => $table->id,
        'status' => 'fechada',
        'priority' => 'normal',
        'total_amount' => 50,
    ]);

    app(TicketPaymentService::class)->pay($ticket, [
        'payment_method' => 'pix',
        'discount_amount' => 0,
        'service_amount' => 0,
    ]);

    expect($ticket->fresh()->status)->toBe('paga')
        ->and($table->fresh()->status)->toBe(RestaurantTable::STATUS_AVAILABLE);
});

it('does not allow assigning admin role through users screen', function () {
    $adminRole = createRole('Administrador', 'administrador');
    $waiterRole = createRole('Garçom', 'garcom');

    $admin = User::factory()->create(['role_id' => $adminRole->id]);
    $target = User::factory()->create(['role_id' => $waiterRole->id]);

    $this->actingAs($admin)
        ->patch(route('users.role.update', $target), ['role_id' => $adminRole->id])
        ->assertSessionHasErrors('role_id');
});
