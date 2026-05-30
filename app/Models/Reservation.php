<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const STATUS_PENDING = 'pendente';
    public const STATUS_CONFIRMED = 'confirmada';
    public const STATUS_CANCELED = 'cancelada';
    public const STATUS_COMPLETED = 'concluida';

    protected $fillable = [
        'restaurant_table_id',
        'customer_name',
        'customer_phone',
        'reserved_at',
        'duration_minutes',
        'party_size',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'reserved_at' => 'datetime',
        ];
    }

    public function restaurantTable(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class);
    }

    public function ticket(): HasOne
    {
        return $this->hasOne(TicketList::class);
    }
}
