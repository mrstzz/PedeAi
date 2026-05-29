<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketItem extends Model
{
    protected $fillable = [
        'ticket_id',
        'product_name',
        'quantity',
        'unit_price',
        'subtotal',
        'status',
        'notes',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'delivered_at' => 'datetime',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(TicketList::class, 'ticket_id');
    }
}
