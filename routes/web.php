<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KitchenQueueController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\TicketListController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('ticket-list', [TicketListController::class, 'index'])->name('ticket-list.index');
    Route::get('ticket-list/create', [TicketListController::class, 'create'])->name('ticket-list.create');
    Route::post('ticket-list/store', [TicketListController::class, 'store'])->name('ticket-list.store');
    Route::get('ticket-list/{ticketList}', [TicketListController::class, 'show'])->name('ticket-list.show');
    Route::patch('ticket-list/{ticketList}/status', [TicketListController::class, 'updateStatus'])->name('ticket-list.status.update');
    Route::post('ticket-list/{ticketList}/items', [TicketListController::class, 'storeItems'])->name('ticket-list.items.store');
    Route::post('ticket-list/{ticketList}/start-preparation', [TicketListController::class, 'startPreparation'])->name('ticket-list.start-preparation');
    Route::post('ticket-items/{ticketItem}/deliver', [TicketListController::class, 'deliverItem'])->name('ticket-items.deliver');

    Route::get('kitchen-queue', KitchenQueueController::class)->name('kitchen-queue.index');

    Route::get('menu-items', [MenuItemController::class, 'index'])->name('menu-items.index');
    Route::get('menu-items/create', [MenuItemController::class, 'create'])->name('menu-items.create');
    Route::post('menu-items/store', [MenuItemController::class, 'store'])->name('menu-items.store');
});

require __DIR__.'/settings.php';
