<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RestaurantTable extends Model
{
    use HasFactory;

    public const STATUS_AVAILABLE = 'disponivel';
    public const STATUS_OCCUPIED = 'ocupada';
    public const STATUS_RESERVED = 'reservada';
    public const STATUS_MAINTENANCE = 'manutencao';

    protected $fillable = [
        'identifier',
        'capacity',
        'status',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(TicketList::class);
    }

    public function activeTicket(): HasOne
    {
        return $this->hasOne(TicketList::class)
            ->whereIn('status', ['aberta', 'em_andamento'])
            ->latestOfMany();
    }
}
