<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketList extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tickets';

    protected $fillable = [
        'customer_name',
        'table_number',
        'status',
        'priority',
        'total_amount',
        'notes',
        'opened_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(TicketItem::class, 'ticket_id');
    }

    protected function displayName(): Attribute
    {
        return Attribute::get(fn (): string => $this->customer_name ?: 'Cliente sem nome');
    }

    public function recalculateTotal(): void
    {
        $this->forceFill([
            'total_amount' => $this->items()->sum('subtotal'),
        ])->save();
    }
}
