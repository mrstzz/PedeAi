<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KitchenQueueController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\RestaurantTableController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TicketListController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('ticket-list', [TicketListController::class, 'index'])->name('ticket-list.index');
    Route::get('ticket-list/create', [TicketListController::class, 'create'])->name('ticket-list.create');
    Route::get('ticket-list/available-tables', [TicketListController::class, 'availableTables'])->name('ticket-list.available-tables');
    Route::post('ticket-list/store', [TicketListController::class, 'store'])->name('ticket-list.store');
    Route::get('ticket-list/{ticketList}', [TicketListController::class, 'show'])->name('ticket-list.show');
    Route::patch('ticket-list/{ticketList}/status', [TicketListController::class, 'updateStatus'])->name('ticket-list.status.update');
    Route::post('ticket-list/{ticketList}/items', [TicketListController::class, 'storeItems'])->name('ticket-list.items.store');
    Route::post('ticket-list/{ticketList}/start-preparation', [TicketListController::class, 'startPreparation'])->name('ticket-list.start-preparation');
    Route::post('ticket-items/{ticketItem}/deliver', [TicketListController::class, 'deliverItem'])->name('ticket-items.deliver');

    Route::get('kitchen-queue', KitchenQueueController::class)->name('kitchen-queue.index');

    Route::get('reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('reservations/store', [ReservationController::class, 'store'])->name('reservations.store');
    Route::patch('reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');

    Route::get('menu-items', [MenuItemController::class, 'index'])->name('menu-items.index');
    Route::get('menu-items/create', [MenuItemController::class, 'create'])->name('menu-items.create');
    Route::post('menu-items/store', [MenuItemController::class, 'store'])->name('menu-items.store');

    Route::get('restaurant-tables', [RestaurantTableController::class, 'index'])->name('restaurant-tables.index');
    Route::get('restaurant-tables/create', [RestaurantTableController::class, 'create'])->name('restaurant-tables.create');
    Route::post('restaurant-tables/store', [RestaurantTableController::class, 'store'])->name('restaurant-tables.store');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::patch('users/{user}/role', [UserController::class, 'updateRole'])->name('users.role.update');
});

require __DIR__.'/settings.php';
